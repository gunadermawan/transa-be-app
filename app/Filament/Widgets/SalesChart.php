<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SalesChart extends ChartWidget
{
    protected ?string $heading = 'Grafik Penjualan (7 Hari Terakhir)';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $user = auth()->user();
        $businessId = $user->business_id;

        // Get last 7 days sales data
        $salesData = Order::whereHas('outlet', fn ($q) => $q->where('business_id', $businessId))
            ->where('status', '!=', 'void')
            ->whereDate('created_at', '>=', now()->subDays(6))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_price) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $salesAmounts = [];
        $transactionCounts = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayName = now()->subDays($i)->locale('id')->isoFormat('ddd, D MMM');

            $labels[] = $dayName;
            $salesAmounts[] = $salesData->get($date)?->total ?? 0;
            $transactionCounts[] = $salesData->get($date)?->count ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Penjualan (Rp)',
                    'data' => $salesAmounts,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Jumlah Transaksi',
                    'data' => $transactionCounts,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'ticks' => [
                        'callback' => 'function(value) { return "Rp " + value.toLocaleString("id-ID"); }',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
        ];
    }
}
