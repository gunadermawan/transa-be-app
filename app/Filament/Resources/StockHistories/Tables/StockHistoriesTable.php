<?php

namespace App\Filament\Resources\StockHistories\Tables;

use App\Models\Outlet;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class StockHistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('stock.product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => 'SKU: '.$record->stock?->product?->sku),

                TextColumn::make('outlet.name')
                    ->label('Outlet')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->outlet?->address),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        'adjustment' => 'warning',
                        'transfer_in' => 'info',
                        'transfer_out' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in' => 'Stock In',
                        'out' => 'Stock Out',
                        'adjustment' => 'Adjustment',
                        'transfer_in' => 'Transfer In',
                        'transfer_out' => 'Transfer Out',
                        default => ucfirst($state),
                    })
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($record, $state): string => ($record->type === 'in' || $record->type === 'transfer_in' ? '+' : '-').$state
                    )
                    ->color(fn ($record): string => $record->type === 'in' || $record->type === 'transfer_in' ? 'success' : 'danger'
                    )
                    ->weight('bold')
                    ->suffix(' units'),

                TextColumn::make('current_stock')
                    ->label('Stock After')
                    ->numeric()
                    ->sortable()
                    ->suffix(' units')
                    ->description('Current stock after this change'),

                TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable()
                    ->copyable()
                    ->placeholder('N/A')
                    ->toggleable(),

                TextColumn::make('user.name')
                    ->label('By User')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('note')
                    ->label('Notes')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn ($state) => $state)
                    ->placeholder('No notes')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->description(fn ($record) => $record->created_at->format('d M Y, H:i')),
            ])
            ->filters([
                SelectFilter::make('outlet_id')
                    ->label('Outlet')
                    ->options(function () {
                        $business = Auth::user()->business;

                        return Outlet::query()
                            ->where('business_id', $business->id)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'in' => 'Stock In',
                        'out' => 'Stock Out',
                        'adjustment' => 'Adjustment',
                        'transfer_in' => 'Transfer In',
                        'transfer_out' => 'Transfer Out',
                    ])
                    ->multiple(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }
}
