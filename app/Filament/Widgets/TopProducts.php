<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopProducts extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Produk Terlaris (30 Hari Terakhir)';

    public function getTableRecordKey($record): string
    {
        return (string) $record->product_id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderItem::query()
                    ->select('product_id')
                    ->selectRaw('SUM(quantity) as total_quantity')
                    ->selectRaw('SUM(quantity * price) as total_revenue')
                    ->selectRaw('COUNT(DISTINCT order_id) as order_count')
                    ->whereHas('order', function ($query) {
                        $query->whereHas('outlet', fn ($q) => $q->where('business_id', auth()->user()->business_id))
                            ->where('status', '!=', 'void')
                            ->where('created_at', '>=', now()->subDays(30));
                    })
                    ->groupBy('product_id')
                    ->orderByDesc('total_quantity')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('product.category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->default('-'),

                TextColumn::make('total_quantity')
                    ->label('Terjual')
                    ->numeric()
                    ->sortable()
                    ->suffix(' item')
                    ->alignEnd()
                    ->color('success'),

                TextColumn::make('order_count')
                    ->label('Transaksi')
                    ->numeric()
                    ->sortable()
                    ->suffix(' order')
                    ->alignEnd(),

                TextColumn::make('total_revenue')
                    ->label('Total Pendapatan')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('product.price')
                    ->label('Harga Satuan')
                    ->money('IDR')
                    ->alignEnd(),
            ])
            ->defaultSort('total_quantity', 'desc')
            ->paginated(false);
    }
}
