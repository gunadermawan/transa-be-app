<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentOrders extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Transaksi Terbaru';

    public function getTableRecordKey($record): string
    {
        return (string) $record->id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->whereHas('outlet', fn ($q) => $q->where('business_id', auth()->user()->business_id))
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('order_number')
                    ->label('No. Order')
                    ->searchable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nomor order disalin!')
                    ->color('primary'),

                TextColumn::make('outlet.name')
                    ->label('Outlet')
                    ->badge()
                    ->color('info')
                    ->default('-'),

                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->default('Walk-in Customer')
                    ->icon('heroicon-m-user'),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('payment_method')
                    ->label('Pembayaran')
                    ->badge()
                    ->colors([
                        'success' => 'cash',
                        'info' => 'qris',
                        'warning' => 'transfer',
                        'primary' => fn ($state) => ! in_array($state, ['cash', 'qris', 'transfer']),
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state ?? 'N/A')),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'completed',
                        'warning' => 'pending',
                        'danger' => 'void',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'completed' => 'Selesai',
                        'pending' => 'Pending',
                        'void' => 'Batal',
                        default => ucfirst($state),
                    }),

                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->since()
                    ->description(fn ($record) => $record->created_at->format('d M Y, H:i')),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
