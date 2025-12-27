<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Outlet;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats(Request $request)
    {
        $user = $request->user();
        $outletId = $request->outlet_id ?? $user->outlet_id;
        $date = $request->date ?? Carbon::today()->toDateString();

        // Get business outlets
        $businessId = $user->business_id;
        $outletIds = $outletId
            ? [$outletId]
            : Outlet::where('business_id', $businessId)->pluck('id')->toArray();

        // TODAY STATS
        $todayOrders = Order::whereIn('outlet_id', $outletIds)
            ->whereDate('created_at', $date)
            ->where('status', 'success')
            ->get();

        $todaySales = $todayOrders->sum('total_price');
        $todayTransactions = $todayOrders->count();
        $todayCustomers = $todayOrders->whereNotNull('customer_id')->unique('customer_id')->count();

        // THIS MONTH STATS
        $startOfMonth = Carbon::parse($date)->startOfMonth();
        $endOfMonth = Carbon::parse($date)->endOfMonth();

        $monthlyOrders = Order::whereIn('outlet_id', $outletIds)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('status', 'success')
            ->get();

        $monthlySales = $monthlyOrders->sum('total_price');
        $monthlyTransactions = $monthlyOrders->count();
        $daysInMonth = $startOfMonth->diffInDays($endOfMonth) + 1;
        $averagePerDay = $daysInMonth > 0 ? $monthlySales / $daysInMonth : 0;

        // ALERTS
        $lowStockProducts = Stock::whereIn('outlet_id', $outletIds)
            ->whereHas('product', function ($query) {
                $query->where('is_stock_managed', true);
            })
            ->get()
            ->filter(function ($stock) {
                return $stock->quantity <= ($stock->product->stock_minimum ?? 10);
            })
            ->count();

        $pendingOrders = Order::whereIn('outlet_id', $outletIds)
            ->where('status', 'pending')
            ->count();

        // TOP PRODUCTS (Today)
        $topProducts = OrderItem::whereHas('order', function ($query) use ($outletIds, $date) {
            $query->whereIn('outlet_id', $outletIds)
                ->whereDate('created_at', $date)
                ->where('status', 'success');
        })
            ->with('product')
            ->selectRaw('product_id, SUM(quantity) as quantity_sold, SUM(total) as revenue')
            ->groupBy('product_id')
            ->orderByDesc('quantity_sold')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? 'Unknown',
                    'quantity_sold' => $item->quantity_sold,
                    'revenue' => $item->revenue,
                ];
            });

        // PAYMENT METHOD BREAKDOWN (Today)
        $paymentMethods = Order::whereIn('outlet_id', $outletIds)
            ->whereDate('created_at', $date)
            ->where('status', 'success')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_price) as total')
            ->groupBy('payment_method')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->payment_method => $item->total];
            });

        return response()->json([
            'today' => [
                'date' => $date,
                'sales' => round($todaySales, 2),
                'transactions' => $todayTransactions,
                'customers' => $todayCustomers,
            ],
            'this_month' => [
                'sales' => round($monthlySales, 2),
                'transactions' => $monthlyTransactions,
                'average_per_day' => round($averagePerDay, 2),
            ],
            'alerts' => [
                'low_stock_count' => $lowStockProducts,
                'pending_orders' => $pendingOrders,
            ],
            'top_products' => $topProducts,
            'payment_methods' => $paymentMethods,
        ]);
    }
}
