<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPaymentRequest;
use App\Http\Requests\StoreQuickServiceOrderItemRequest;
use App\Http\Requests\StoreQuickServiceOrderRequest;
use App\Http\Requests\UpdateQuickServiceOrderItemRequest;
use App\Http\Requests\UpdateQuickServiceOrderRequest;
use App\Http\Resources\QuickServiceOrderResource;
use App\Models\BusinessSetting;
use App\Models\Product;
use App\Models\QuickServiceOrder;
use App\Models\QuickServiceOrderItem;
use App\Models\Stock;
use App\Models\StockHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuickServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = QuickServiceOrder::with(['items', 'cashier'])
            ->where('outlet_id', $request->user()->outlet_id);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('mode') && $request->mode !== 'all') {
            $query->where('type', $request->mode === 'dine_in' ? 'DINE IN' : 'TAKE AWAY');
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $perPage = $request->input('per_page', 20);
        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $summary = [
            'total_draft' => QuickServiceOrder::where('outlet_id', $request->user()->outlet_id)
                ->where('status', 'draft')->count(),
            'total_completed' => QuickServiceOrder::where('outlet_id', $request->user()->outlet_id)
                ->where('status', 'completed')->count(),
            'total_dine_in' => QuickServiceOrder::where('outlet_id', $request->user()->outlet_id)
                ->where('type', 'DINE IN')->count(),
            'total_take_away' => QuickServiceOrder::where('outlet_id', $request->user()->outlet_id)
                ->where('type', 'TAKE AWAY')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => [
                'orders' => QuickServiceOrderResource::collection($orders),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'total_pages' => $orders->lastPage(),
                ],
                'summary' => $summary,
            ],
        ]);
    }

    public function store(StoreQuickServiceOrderRequest $request): JsonResponse
    {
        $user = $request->user();
        $outletId = $user->outlet_id;

        $businessSettings = BusinessSetting::where('business_id', $user->business_id)->get();
        $taxPercentage = $businessSettings->where('type', 'tax')->first()?->value ?? 0;
        $serviceChargePercentage = $businessSettings->where('type', 'service')->first()?->value ?? 0;

        $order = new QuickServiceOrder([
            'outlet_id' => $outletId,
            'cashier_id' => $user->id,
            'type' => $request->type,
            'pax' => $request->pax,
            'table_number' => $request->table_number,
            'customer_name' => $request->customer_name,
            'notes' => $request->notes,
            'tax_percentage' => $taxPercentage,
            'service_charge_percentage' => $serviceChargePercentage,
        ]);

        $order->order_number = $order->generateOrderNumber();
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => [
                'order' => new QuickServiceOrderResource($order->load(['items', 'cashier'])),
            ],
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $order = QuickServiceOrder::with(['items', 'cashier'])
            ->where('outlet_id', $request->user()->outlet_id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Order detail retrieved successfully',
            'data' => [
                'order' => new QuickServiceOrderResource($order),
            ],
        ]);
    }

    public function update(UpdateQuickServiceOrderRequest $request, int $id): JsonResponse
    {
        $order = QuickServiceOrder::where('outlet_id', $request->user()->outlet_id)
            ->where('status', 'draft')
            ->findOrFail($id);

        $order->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'data' => [
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'pax' => $order->pax,
                    'table_number' => $order->table_number,
                    'customer_name' => $order->customer_name,
                    'notes' => $order->notes,
                    'updated_at' => $order->updated_at->toIso8601String(),
                ],
            ],
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $order = QuickServiceOrder::where('outlet_id', $request->user()->outlet_id)
            ->findOrFail($id);

        if ($order->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete completed order',
                'errors' => [
                    'order' => ['Only draft orders can be deleted'],
                ],
            ], 400);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully',
        ]);
    }

    public function addItem(StoreQuickServiceOrderItemRequest $request, int $orderId): JsonResponse
    {
        $order = QuickServiceOrder::where('outlet_id', $request->user()->outlet_id)
            ->where('status', 'draft')
            ->findOrFail($orderId);

        $product = Product::findOrFail($request->product_id);

        $item = $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_image' => $product->image,
            'quantity' => $request->quantity,
            'price' => $product->price,
            'subtotal' => $product->price * $request->quantity,
            'notes' => $request->notes,
        ]);

        $order->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Item added to order successfully',
            'data' => [
                'order_item' => [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'price' => (float) $item->price,
                    'subtotal' => (float) $item->subtotal,
                    'notes' => $item->notes,
                ],
                'order' => [
                    'id' => $order->id,
                    'items_count' => $order->items->count(),
                    'subtotal' => (float) $order->subtotal,
                    'tax' => (float) $order->tax,
                    'service_charge' => (float) $order->service_charge,
                    'total' => (float) $order->total,
                ],
            ],
        ], 201);
    }

    public function updateItem(UpdateQuickServiceOrderItemRequest $request, int $orderId, int $itemId): JsonResponse
    {
        $order = QuickServiceOrder::where('outlet_id', $request->user()->outlet_id)
            ->where('status', 'draft')
            ->findOrFail($orderId);

        $item = QuickServiceOrderItem::where('quick_service_order_id', $order->id)
            ->findOrFail($itemId);

        $item->quantity = $request->quantity;
        $item->subtotal = $item->price * $request->quantity;
        if ($request->has('notes')) {
            $item->notes = $request->notes;
        }
        $item->save();

        $order->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Order item updated successfully',
            'data' => [
                'order_item' => [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                    'notes' => $item->notes,
                ],
                'order' => [
                    'subtotal' => (float) $order->subtotal,
                    'tax' => (float) $order->tax,
                    'service_charge' => (float) $order->service_charge,
                    'total' => (float) $order->total,
                ],
            ],
        ]);
    }

    public function deleteItem(Request $request, int $orderId, int $itemId): JsonResponse
    {
        $order = QuickServiceOrder::where('outlet_id', $request->user()->outlet_id)
            ->where('status', 'draft')
            ->findOrFail($orderId);

        $item = QuickServiceOrderItem::where('quick_service_order_id', $order->id)
            ->findOrFail($itemId);

        $item->delete();

        $order->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Order item removed successfully',
            'data' => [
                'order' => [
                    'id' => $order->id,
                    'items_count' => $order->items->count(),
                    'subtotal' => (float) $order->subtotal,
                    'tax' => (float) $order->tax,
                    'service_charge' => (float) $order->service_charge,
                    'total' => (float) $order->total,
                ],
            ],
        ]);
    }

    public function save(Request $request, int $id): JsonResponse
    {
        $order = QuickServiceOrder::where('outlet_id', $request->user()->outlet_id)
            ->where('status', 'draft')
            ->findOrFail($id);

        if ($request->has('notes')) {
            $order->notes = $request->notes;
        }
        $order->saved_at = now();
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order saved successfully',
            'data' => [
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'saved_at' => $order->saved_at->toIso8601String(),
                ],
            ],
        ]);
    }

    public function processPayment(ProcessPaymentRequest $request, int $id): JsonResponse
    {
        $order = QuickServiceOrder::where('outlet_id', $request->user()->outlet_id)
            ->where('status', 'draft')
            ->findOrFail($id);

        if ($order->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot process payment for empty order',
                'errors' => [
                    'order' => ['Order must have at least one item'],
                ],
            ], 400);
        }

        $order->payment_method = $request->payment_method;
        $order->paid_amount = $request->amount_paid;
        $order->change = max(0, $request->amount_paid - $order->total);
        $order->payment_status = 'paid';
        $order->status = 'completed';
        $order->invoice_number = $order->generateInvoiceNumber();
        $order->save();

        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);

            if ($product && $product->is_stock_managed) {
                $stock = Stock::where('product_id', $item->product_id)
                    ->where('outlet_id', $order->outlet_id)
                    ->first();

                if ($stock) {
                    $previousStock = $stock->quantity;
                    $stock->quantity -= $item->quantity;
                    $stock->save();

                    StockHistory::create([
                        'stock_id' => $stock->id,
                        'user_id' => $request->user()->id,
                        'outlet_id' => $order->outlet_id,
                        'quantity' => $item->quantity,
                        'current_stock' => $stock->quantity,
                        'previous_stock' => $previousStock,
                        'type' => 'out',
                        'reference_type' => 'quick_service_order',
                        'reference_id' => $order->id,
                        'notes' => 'Quick Service Order: '.$order->order_number,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully',
            'data' => [
                'payment' => [
                    'id' => $order->id,
                    'order_id' => $order->id,
                    'payment_method' => $order->payment_method,
                    'amount_paid' => (float) $order->paid_amount,
                    'amount_due' => (float) $order->total,
                    'change' => (float) $order->change,
                    'payment_date' => $order->updated_at->toIso8601String(),
                ],
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'invoice_number' => $order->invoice_number,
                    'status' => $order->status,
                    'total' => (float) $order->total,
                    'paid_amount' => (float) $order->paid_amount,
                    'change' => (float) $order->change,
                    'payment_status' => $order->payment_status,
                ],
            ],
        ], 201);
    }

    public function getReceipt(Request $request, int $id): JsonResponse
    {
        $order = QuickServiceOrder::with(['items', 'cashier', 'outlet'])
            ->where('outlet_id', $request->user()->outlet_id)
            ->where('status', 'completed')
            ->findOrFail($id);

        $outlet = $order->outlet;
        $business = $outlet->business;

        $receipt = [
            'order_number' => $order->order_number,
            'invoice_number' => $order->invoice_number,
            'type' => $order->type,
            'pax' => $order->pax,
            'table_number' => $order->table_number,
            'customer_name' => $order->customer_name,
            'date' => $order->created_at->format('Y-m-d'),
            'time' => $order->created_at->format('H:i:s'),
            'cashier' => $order->cashier->name,
            'items' => $order->items->map(function ($item) {
                return [
                    'quantity' => $item->quantity,
                    'name' => $item->product_name,
                    'price' => (float) $item->price,
                    'subtotal' => (float) $item->subtotal,
                ];
            }),
            'subtotal' => (float) $order->subtotal,
            'tax' => [
                'name' => 'PB1',
                'percentage' => (float) $order->tax_percentage,
                'amount' => (float) $order->tax,
            ],
            'service_charge' => [
                'name' => 'Service Charge',
                'percentage' => (float) $order->service_charge_percentage,
                'amount' => (float) $order->service_charge,
            ],
            'discount' => (float) $order->discount,
            'total' => (float) $order->total,
            'payment_method' => $order->payment_method,
            'amount_paid' => (float) $order->paid_amount,
            'change' => (float) $order->change,
            'outlet' => [
                'name' => $outlet->name,
                'address' => $outlet->address ?? '',
                'phone' => $outlet->phone ?? '',
            ],
            'footer_text' => 'Terima kasih atas kunjungan Anda',
        ];

        return response()->json([
            'success' => true,
            'message' => 'Receipt data retrieved successfully',
            'data' => [
                'receipt' => $receipt,
            ],
        ]);
    }

    public function printKitchen(Request $request, int $id): JsonResponse
    {
        $order = QuickServiceOrder::where('outlet_id', $request->user()->outlet_id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Kitchen order printed successfully',
            'data' => [
                'print_job_id' => 'PRINT-'.uniqid(),
                'printed_at' => now()->toIso8601String(),
                'printer' => [
                    'id' => $request->printer_id ?? null,
                    'name' => 'Kitchen Printer 1',
                    'location' => 'Dapur Utama',
                ],
            ],
        ]);
    }

    public function printReceipt(Request $request, int $id): JsonResponse
    {
        $order = QuickServiceOrder::where('outlet_id', $request->user()->outlet_id)
            ->where('status', 'completed')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Receipt printed successfully',
            'data' => [
                'print_job_id' => 'PRINT-'.uniqid(),
                'printed_at' => now()->toIso8601String(),
                'email_sent' => $request->send_email ?? false,
            ],
        ]);
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => ['required', 'string'],
        ]);

        $order = QuickServiceOrder::where('outlet_id', $request->user()->outlet_id)
            ->findOrFail($id);

        $order->status = 'cancelled';
        $order->cancelled_at = now();
        $order->cancel_reason = $request->reason;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => [
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'cancelled_at' => $order->cancelled_at->toIso8601String(),
                    'cancel_reason' => $order->cancel_reason,
                ],
            ],
        ]);
    }

    public function statistics(Request $request): JsonResponse
    {
        $query = QuickServiceOrder::where('outlet_id', $request->user()->outlet_id)
            ->where('status', 'completed');

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
        } elseif (! $request->has('date_from') && ! $request->has('date_to')) {
            $query->whereDate('created_at', today());
        }

        if ($request->has('mode') && $request->mode !== 'all') {
            $query->where('type', $request->mode === 'dine_in' ? 'DINE IN' : 'TAKE AWAY');
        }

        $orders = $query->get();

        $dineInOrders = $orders->where('type', 'DINE IN');
        $takeAwayOrders = $orders->where('type', 'TAKE AWAY');

        $topProducts = QuickServiceOrderItem::whereIn('quick_service_order_id', $orders->pluck('id'))
            ->selectRaw('product_id, product_name, SUM(quantity) as quantity_sold, SUM(subtotal) as revenue')
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('quantity_sold')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Statistics retrieved successfully',
            'data' => [
                'today' => [
                    'total_orders' => $orders->count(),
                    'total_revenue' => (float) $orders->sum('total'),
                    'dine_in' => [
                        'orders' => $dineInOrders->count(),
                        'revenue' => (float) $dineInOrders->sum('total'),
                    ],
                    'take_away' => [
                        'orders' => $takeAwayOrders->count(),
                        'revenue' => (float) $takeAwayOrders->sum('total'),
                    ],
                    'avg_order_value' => $orders->count() > 0 ? (float) ($orders->sum('total') / $orders->count()) : 0,
                    'avg_pax' => $orders->where('pax', '>', 0)->count() > 0 ? (float) ($orders->sum('pax') / $orders->where('pax', '>', 0)->count()) : 0,
                ],
                'top_products' => $topProducts->map(function ($product) {
                    return [
                        'product_id' => $product->product_id,
                        'product_name' => $product->product_name,
                        'quantity_sold' => (int) $product->quantity_sold,
                        'revenue' => (float) $product->revenue,
                    ];
                }),
            ],
        ]);
    }
}
