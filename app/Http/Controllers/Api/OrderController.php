<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockHistory;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private SubscriptionService $subscriptionService) {}

    // add order
    public function addOrder(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|integer',
            'sub_total' => 'required|numeric',
            'total_price' => 'required|numeric',
            'total_items' => 'required|integer',
            'tax' => 'required|numeric',
            'discount' => 'required|numeric',
            'payment_method' => 'required|string|in:cash,card,qris,transfer',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        $business = $request->user()->business;

        // Check subscription limit for transactions
        if (! $this->subscriptionService->canAddTransaction($business)) {
            $subscription = $this->subscriptionService->getActiveSubscription($business);
            $maxTransactions = $subscription?->plan->max_transactions_per_month ?? 0;

            return response()->json([
                'message' => "You have reached the maximum number of transactions ($maxTransactions) for this month. Please upgrade your plan.",
                'code' => 'TRANSACTION_LIMIT_REACHED',
                'current_plan' => $subscription?->plan->name,
                'max_transactions_per_month' => $maxTransactions,
            ], 403);
        }

        // Generate order number with date
        $orderNumber = 'ORD-'.date('Ymd').'-'.str_pad(Order::whereDate('created_at', today())->count() + 1, 6, '0', STR_PAD_LEFT);

        $order = Order::create([
            'order_number' => $orderNumber,
            'outlet_id' => $request->outlet_id,
            'customer_id' => $request->customer_id, // optional
            'sub_total' => $request->sub_total,
            'total_price' => $request->total_price,
            'total_items' => $request->total_items,
            'tax' => $request->tax,
            'discount' => $request->discount,
            'payment_method' => $request->payment_method,
            'amount_received' => $request->amount_received ?? $request->total_price,
            'notes' => $request->notes, // optional
            'status' => 'success',
            'cashier_id' => $request->user()->id,
        ]);

        // add order items
        foreach ($request->items as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['total'] ?? ($item['price'] * $item['quantity']),
                'notes' => $item['notes'] ?? null,
            ]);
        }

        // update stock (only for stock-managed products)
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);

            // Only reduce stock if product is stock-managed
            if ($product && $product->is_stock_managed) {
                $stock = Stock::where('product_id', $item['product_id'])
                    ->where('outlet_id', $request->outlet_id)
                    ->first();

                if ($stock) {
                    $previousStock = $stock->quantity;
                    $stock->quantity -= $item['quantity'];
                    $stock->save();

                    // create stock history
                    StockHistory::create([
                        'stock_id' => $stock->id,
                        'user_id' => $request->user()->id,
                        'outlet_id' => $request->outlet_id,
                        'quantity' => $item['quantity'],
                        'current_stock' => $stock->quantity,
                        'type' => 'deduct',
                        'reference' => $order->order_number,
                        'note' => 'Order #'.$order->order_number,
                    ]);
                }
            }
        }

        // Calculate change for cash payments
        $change = 0;
        if ($request->payment_method === 'cash' && $request->amount_received) {
            $change = $request->amount_received - $request->total_price;
        }

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'grand_total' => $order->total_price,
                'payment_method' => $order->payment_method,
                'amount_received' => $order->amount_received,
                'change' => $change,
                'created_at' => $order->created_at->toDateTimeString(),
            ],
        ], 201);
    }

    // get orders for outlet
    public function getOrdersByOutlet($id)
    {
        $orders = Order::where('outlet_id', $id)->orderBy('id', 'desc')->get();

        // load order items, product
        $orders->load('items.product');

        return response()->json([
            'data' => $orders,
        ]);
    }

    // get all orders (with filters)
    public function getOrders(Request $request)
    {
        $query = Order::query();

        // Filter by outlet
        if ($request->has('outlet_id')) {
            $query->where('outlet_id', $request->outlet_id);
        }

        // Filter by business (via outlet)
        if ($request->user()->business_id) {
            $query->whereHas('outlet', function ($q) use ($request) {
                $q->where('business_id', $request->user()->business_id);
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search by order number
        if ($request->has('search')) {
            $query->where('order_number', 'like', '%'.$request->search.'%');
        }

        $orders = $query->with(['items.product', 'outlet', 'cashier', 'customer'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json($orders);
    }

    // get single order
    public function getOrder($id)
    {
        // Use query() to respect global scope
        $order = Order::query()
            ->with(['items.product', 'outlet', 'cashier', 'customer'])
            ->findOrFail($id);

        return response()->json([
            'data' => $order,
        ]);
    }

    // delete/void order
    public function deleteOrder($id)
    {
        // Use query() to respect global scope
        $order = Order::query()->with('items')->findOrFail($id);

        // Return stock if order is voided
        foreach ($order->items as $item) {
            $stock = Stock::where('product_id', $item->product_id)
                ->where('outlet_id', $order->outlet_id)
                ->first();

            if ($stock) {
                $stock->quantity += $item->quantity;
                $stock->save();

                // Create stock history for void
                StockHistory::create([
                    'stock_id' => $stock->id,
                    'user_id' => auth()->id(),
                    'outlet_id' => $order->outlet_id,
                    'quantity' => $item->quantity,
                    'current_stock' => $stock->quantity,
                    'type' => 'void',
                    'reference' => $order->order_number,
                    'note' => 'Void Order #'.$order->order_number,
                ]);
            }
        }

        // Mark as void instead of deleting
        $order->status = 'void';
        $order->save();

        return response()->json([
            'message' => 'Order voided successfully',
            'data' => $order,
        ]);
    }
}
