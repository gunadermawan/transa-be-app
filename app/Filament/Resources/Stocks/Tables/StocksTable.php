<?php

namespace App\Filament\Resources\Stocks\Tables;

use App\Models\Outlet;
use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class StocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('outlet.name')
                    ->label('Outlet')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->outlet?->address),

                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => 'SKU: '.$record->product?->sku),

                TextColumn::make('product.category.name')
                    ->label('Category')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('quantity')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state <= 10 => 'danger',
                        $state <= 50 => 'warning',
                        default => 'success',
                    })
                    ->suffix(' units'),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(),
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

                SelectFilter::make('product_id')
                    ->label('Product')
                    ->options(function () {
                        $business = Auth::user()->business;

                        return Product::query()
                            ->where('business_id', $business->id)
                            ->get()
                            ->mapWithKeys(function ($product) {
                                return [$product->id => $product->name.' ('.$product->sku.')'];
                            });
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make('stock_level')
                    ->label('Stock Level')
                    ->options([
                        'low' => 'Low (≤10)',
                        'medium' => 'Medium (11-50)',
                        'high' => 'High (>50)',
                    ])
                    ->query(function ($query, $value) {
                        return match ($value['value'] ?? null) {
                            'low' => $query->where('quantity', '<=', 10),
                            'medium' => $query->whereBetween('quantity', [11, 50]),
                            'high' => $query->where('quantity', '>', 50),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }
}
