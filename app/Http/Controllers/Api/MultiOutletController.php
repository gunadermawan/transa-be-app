<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Outlet;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MultiOutletController extends Controller
{
    /**
     * Get all outlets accessible by user
     * For Owner/Manager: all outlets in business
     * For Cashier: only their assigned outlet
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyOutlets(Request $request)
    {
        $user = $request->user();

        // Super admin can see all
        if ($user->role_id == 1) { // Assuming 1 = super_admin
            $outlets = Outlet::all();
        }
        // Owner/Manager: all outlets in their business
        elseif (in_array($user->role_id, [2, 3])) { // 2 = business_owner, 3 = manager
            $outlets = Outlet::where('business_id', $user->business_id)
                ->withCount('orders')
                ->with('business')
                ->get();
        }
        // Cashier/Staff: only their outlet
        else {
            $outlets = Outlet::where('id', $user->outlet_id)
                ->withCount('orders')
                ->with('business')
                ->get();
        }

        return response()->json([
            'data' => $outlets,
            'current_outlet_id' => $user->outlet_id,
        ]);
    }

    /**
     * Switch outlet (update user's active outlet)
     * Used when user wants to work on different outlet
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function switchOutlet(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|integer|exists:outlets,id',
        ]);

        $user = $request->user();
        $outletId = $request->outlet_id;

        // Verify user has access to this outlet
        $outlet = Outlet::find($outletId);

        // Super admin can switch to any outlet
        if ($user->role_id == 1) {
            // OK
        }
        // Owner/Manager can only switch within their business
        elseif (in_array($user->role_id, [2, 3])) {
            if ($outlet->business_id != $user->business_id) {
                return response()->json([
                    'message' => 'You do not have access to this outlet',
                ], 403);
            }
        }
        // Cashier/Staff can only use their assigned outlet
        else {
            if ($outlet->id != $user->outlet_id) {
                return response()->json([
                    'message' => 'You can only access your assigned outlet',
                ], 403);
            }
        }

        // Update user's active outlet (this could be stored in session or user preferences)
        $user->outlet_id = $outletId;
        $user->save();

        return response()->json([
            'message' => 'Switched to '.$outlet->name,
            'data' => [
                'outlet_id' => $outlet->id,
                'outlet_name' => $outlet->name,
                'business_id' => $outlet->business_id,
            ],
        ]);
    }

    /**
     * Cross-Outlet Dashboard
     * Compare all outlets performance
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function crossOutletDashboard(Request $request)
    {
        $user = $request->user();
        $date = $request->date ?? Carbon::today()->toDateString();

        // Get outlets based on user role
        if ($user->role_id == 1) {
            // Super admin: all outlets
            $businessId = $request->business_id;
            $outlets = $businessId
                ? Outlet::where('business_id', $businessId)->get()
                : Outlet::all();
        } else {
            // Owner/Manager: their business outlets
            $outlets = Outlet::where('business_id', $user->business_id)->get();
        }

        $outletStats = [];

        foreach ($outlets as $outlet) {
            // Today's stats
            $todayOrders = Order::where('outlet_id', $outlet->id)
                ->whereDate('created_at', $date)
                ->where('status', 'success')
                ->get();

            // This month stats
            $startOfMonth = Carbon::parse($date)->startOfMonth();
            $endOfMonth = Carbon::parse($date)->endOfMonth();

            $monthlyOrders = Order::where('outlet_id', $outlet->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('status', 'success')
                ->get();

            // Low stock count
            $lowStockCount = Stock::where('outlet_id', $outlet->id)
                ->whereHas('product', function ($query) {
                    $query->where('is_stock_managed', true);
                })
                ->get()
                ->filter(function ($stock) {
                    return $stock->quantity <= ($stock->product->stock_minimum ?? 10);
                })
                ->count();

            $outletStats[] = [
                'outlet_id' => $outlet->id,
                'outlet_name' => $outlet->name,
                'address' => $outlet->address,
                'today' => [
                    'sales' => round($todayOrders->sum('total_price'), 2),
                    'transactions' => $todayOrders->count(),
                ],
                'this_month' => [
                    'sales' => round($monthlyOrders->sum('total_price'), 2),
                    'transactions' => $monthlyOrders->count(),
                ],
                'alerts' => [
                    'low_stock_count' => $lowStockCount,
                ],
            ];
        }

        // Sort by today's sales (highest first)
        usort($outletStats, function ($a, $b) {
            return $b['today']['sales'] <=> $a['today']['sales'];
        });

        // Total across all outlets
        $totalToday = array_sum(array_column(array_column($outletStats, 'today'), 'sales'));
        $totalMonth = array_sum(array_column(array_column($outletStats, 'this_month'), 'sales'));

        return response()->json([
            'date' => $date,
            'total_outlets' => count($outletStats),
            'grand_total' => [
                'today' => round($totalToday, 2),
                'this_month' => round($totalMonth, 2),
            ],
            'outlets' => $outletStats,
        ]);
    }

    /**
     * Outlet Comparison Report
     * Compare specific metrics across outlets
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function outletComparison(Request $request)
    {
        $user = $request->user();
        $startDate = $request->start_date ?? Carbon::today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? Carbon::today()->toDateString();

        // Get outlets
        if ($user->role_id == 1) {
            $businessId = $request->business_id;
            $outlets = $businessId
                ? Outlet::where('business_id', $businessId)->get()
                : Outlet::all();
        } else {
            $outlets = Outlet::where('business_id', $user->business_id)->get();
        }

        $comparison = [];

        foreach ($outlets as $outlet) {
            $orders = Order::where('outlet_id', $outlet->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'success')
                ->get();

            $orderItems = OrderItem::whereIn('order_id', $orders->pluck('id'))
                ->with('product')
                ->get();

            // Calculate metrics
            $totalSales = $orders->sum('total_price');
            $totalTransactions = $orders->count();
            $totalItems = $orderItems->sum('quantity');
            $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

            // Payment methods breakdown
            $paymentMethods = $orders->groupBy('payment_method')->map(function ($group) {
                return $group->sum('total_price');
            });

            // Best selling product
            $bestSelling = $orderItems->groupBy('product_id')
                ->map(function ($items) {
                    return [
                        'product_name' => $items->first()->product->name ?? 'Unknown',
                        'quantity' => $items->sum('quantity'),
                        'revenue' => $items->sum('total'),
                    ];
                })
                ->sortByDesc('quantity')
                ->values()
                ->take(5);

            $comparison[] = [
                'outlet_id' => $outlet->id,
                'outlet_name' => $outlet->name,
                'address' => $outlet->address,
                'metrics' => [
                    'total_sales' => round($totalSales, 2),
                    'total_transactions' => $totalTransactions,
                    'total_items_sold' => $totalItems,
                    'average_transaction' => round($averageTransaction, 2),
                ],
                'payment_methods' => $paymentMethods,
                'best_selling_products' => $bestSelling,
            ];
        }

        // Sort by total sales
        usort($comparison, function ($a, $b) {
            return $b['metrics']['total_sales'] <=> $a['metrics']['total_sales'];
        });

        return response()->json([
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'comparison' => $comparison,
        ]);
    }

    /**
     * Get outlet performance ranking
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function outletRanking(Request $request)
    {
        $user = $request->user();
        $period = $request->period ?? 'today'; // today, week, month, year

        // Date range based on period
        switch ($period) {
            case 'week':
                $startDate = Carbon::today()->subDays(7);
                break;
            case 'month':
                $startDate = Carbon::today()->subDays(30);
                break;
            case 'year':
                $startDate = Carbon::today()->subYear();
                break;
            default: // today
                $startDate = Carbon::today();
        }

        // Get outlets
        if ($user->role_id == 1) {
            $businessId = $request->business_id;
            $outlets = $businessId
                ? Outlet::where('business_id', $businessId)->get()
                : Outlet::all();
        } else {
            $outlets = Outlet::where('business_id', $user->business_id)->get();
        }

        $ranking = [];

        foreach ($outlets as $outlet) {
            $sales = Order::where('outlet_id', $outlet->id)
                ->where('created_at', '>=', $startDate)
                ->where('status', 'success')
                ->sum('total_price');

            $transactions = Order::where('outlet_id', $outlet->id)
                ->where('created_at', '>=', $startDate)
                ->where('status', 'success')
                ->count();

            $ranking[] = [
                'outlet_id' => $outlet->id,
                'outlet_name' => $outlet->name,
                'sales' => round($sales, 2),
                'transactions' => $transactions,
            ];
        }

        // Sort by sales
        usort($ranking, function ($a, $b) {
            return $b['sales'] <=> $a['sales'];
        });

        // Add rank
        foreach ($ranking as $index => &$item) {
            $item['rank'] = $index + 1;
        }

        return response()->json([
            'period' => $period,
            'ranking' => $ranking,
        ]);
    }
}
