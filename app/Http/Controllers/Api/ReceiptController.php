<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    /**
     * Get formatted receipt data for printing
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReceipt($orderId)
    {
        $order = Order::with([
            'items.product',
            'outlet.business',
            'cashier',
            'customer',
        ])->findOrFail($orderId);

        $outlet = $order->outlet;
        $business = $outlet->business;

        // Format items for receipt
        $items = $order->items->map(function ($item) {
            return [
                'name' => $item->product->name ?? 'Unknown Product',
                'quantity' => $item->quantity,
                'price' => $item->price,
                'subtotal' => $item->total,
                'notes' => $item->notes ?? null,
            ];
        });

        // Payment info
        $paymentInfo = [
            'method' => ucfirst($order->payment_method),
            'subtotal' => $order->sub_total,
            'discount' => $order->discount,
            'tax' => $order->tax,
            'grand_total' => $order->total_price,
        ];

        // Add amount_received and change for cash payments
        if ($order->payment_method === 'cash') {
            $paymentInfo['amount_received'] = $order->amount_received ?? $order->total_price;
            $paymentInfo['change'] = ($order->amount_received ?? $order->total_price) - $order->total_price;
        }

        return response()->json([
            'business' => [
                'name' => $business->name ?? 'Business Name',
                'outlet_name' => $outlet->name ?? 'Outlet Name',
                'address' => $outlet->address ?? $business->address ?? '-',
                'phone' => $outlet->phone ?? $business->phone ?? '-',
                'tax_id' => $business->tax_id ?? null,
                'logo_url' => $business->logo ? url($business->logo) : null,
            ],
            'transaction' => [
                'order_number' => $order->order_number,
                'date' => $order->created_at->format('d F Y'),
                'time' => $order->created_at->format('H:i:s'),
                'cashier' => $order->cashier->name ?? 'Unknown',
                'customer' => $order->customer->name ?? null,
            ],
            'items' => $items,
            'summary' => [
                'subtotal' => $order->sub_total,
                'discount' => $order->discount,
                'tax' => $order->tax,
                'grand_total' => $order->total_price,
            ],
            'payment' => $paymentInfo,
            'footer' => [
                'message' => 'Terima kasih atas kunjungan Anda!',
                'social_media' => $business->social_media ?? null,
            ],
        ]);
    }

    /**
     * Get multiple receipts (for batch printing)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReceipts(Request $request)
    {
        $orderIds = $request->order_ids ?? [];

        $receipts = collect($orderIds)->map(function ($orderId) {
            $order = Order::with([
                'items.product',
                'outlet.business',
                'cashier',
                'customer',
            ])->find($orderId);

            if (! $order) {
                return null;
            }

            return $this->formatReceipt($order);
        })->filter();

        return response()->json([
            'receipts' => $receipts,
        ]);
    }

    /**
     * Format receipt data
     *
     * @param  Order  $order
     * @return array
     */
    private function formatReceipt($order)
    {
        $outlet = $order->outlet;
        $business = $outlet->business;

        return [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'business_name' => $business->name ?? 'Business Name',
            'outlet_name' => $outlet->name ?? 'Outlet Name',
            'address' => $outlet->address ?? $business->address ?? '-',
            'phone' => $outlet->phone ?? $business->phone ?? '-',
            'date' => $order->created_at->format('d F Y H:i:s'),
            'cashier' => $order->cashier->name ?? 'Unknown',
            'items' => $order->items->map(function ($item) {
                return [
                    'name' => $item->product->name ?? 'Unknown',
                    'qty' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->total,
                ];
            }),
            'subtotal' => $order->sub_total,
            'discount' => $order->discount,
            'tax' => $order->tax,
            'grand_total' => $order->total_price,
            'payment_method' => ucfirst($order->payment_method),
        ];
    }
}
