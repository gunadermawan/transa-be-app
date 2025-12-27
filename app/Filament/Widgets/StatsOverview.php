<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();
        $businessId = $user->business_id;

        // Today's sales
        $todaySales = Order::whereHas('outlet', fn ($q) => $q->where('business_id', $businessId))
            ->whereDate('created_at', today())
            ->where('status', '!=', 'void')
            ->sum('total_price');

        // Yesterday's sales for comparison
        $yesterdaySales = Order::whereHas('outlet', fn ($q) => $q->where('business_id', $businessId))
            ->whereDate('created_at', today()->subDay())
            ->where('status', '!=', 'void')
            ->sum('total_price');

        $salesChange = $yesterdaySales > 0
            ? (($todaySales - $yesterdaySales) / $yesterdaySales) * 100
            : 0;

        // Today's transactions
        $todayTransactions = Order::whereHas('outlet', fn ($q) => $q->where('business_id', $businessId))
            ->whereDate('created_at', today())
            ->where('status', '!=', 'void')
            ->count();

        $yesterdayTransactions = Order::whereHas('outlet', fn ($q) => $q->where('business_id', $businessId))
            ->whereDate('created_at', today()->subDay())
            ->where('status', '!=', 'void')
            ->count();

        $transactionsChange = $yesterdayTransactions > 0
            ? (($todayTransactions - $yesterdayTransactions) / $yesterdayTransactions) * 100
            : 0;

        // Total products
        $totalProducts = Product::where('business_id', $businessId)->count();

        // Total customers
        $totalCustomers = Customer::where('business_id', $businessId)->count();

        // This month revenue
        $monthRevenue = Order::whereHas('outlet', fn ($q) => $q->where('business_id', $businessId))
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', '!=', 'void')
            ->sum('total_price');

        return [
            Stat::make('Penjualan Hari Ini', 'Rp '.number_format($todaySales, 0, ',', '.'))
                ->description($salesChange >= 0 ? '+'.number_format($salesChange, 1).'% dari kemarin' : number_format($salesChange, 1).'% dari kemarin')
                ->descriptionIcon($salesChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($salesChange >= 0 ? 'success' : 'danger')
                ->chart([7, 12, 8, 15, 14, 18, $todaySales > 0 ? 20 : 10]),

            Stat::make('Transaksi Hari Ini', number_format($todayTransactions))
                ->description($transactionsChange >= 0 ? '+'.number_format($transactionsChange, 1).'% dari kemarin' : number_format($transactionsChange, 1).'% dari kemarin')
                ->descriptionIcon($transactionsChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($transactionsChange >= 0 ? 'success' : 'danger')
                ->chart([3, 5, 4, 7, 6, 8, $todayTransactions > 0 ? 10 : 5]),

            Stat::make('Pendapatan Bulan Ini', 'Rp '.number_format($monthRevenue, 0, ',', '.'))
                ->description('Total revenue bulan '.now()->format('F'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart([100, 120, 110, 150, 140, 180, $monthRevenue > 0 ? 200 : 100]),

            Stat::make('Total Produk', number_format($totalProducts))
                ->description('Produk aktif')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Total Customer', number_format($totalCustomers))
                ->description('Customer terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Status Trial', $user->business?->subscription_status === 'trial' ? 'Aktif' : 'Berlangganan')
                ->description($user->business?->expired_at ? 'Berakhir: '.now()->parse($user->business->expired_at)->format('d M Y') : '-')
                ->descriptionIcon('heroicon-m-clock')
                ->color($user->business?->subscription_status === 'trial' ? 'warning' : 'success'),
        ];
    }
}
