<?php

namespace App\Filament\Resources\ProductVariants\Tables;

use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ProductVariantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder-product.png'))
                    ->size(50),

                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => 'SKU: '.$record->product?->sku)
                    ->weight('bold'),

                TextColumn::make('variant_name')
                    ->label('Variant')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => 'SKU: '.$record->sku)
                    ->copyable(),

                TextColumn::make('attributes')
                    ->label('Attributes')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? collect($state)->map(fn ($value, $key) => "$key: $value")->join(', ') : 'No attributes')
                    ->color('gray')
                    ->limit(30)
                    ->tooltip(fn ($state) => $state ? collect($state)->map(fn ($value, $key) => "$key: $value")->join(', ') : null)
                    ->toggleable(),

                TextColumn::make('price_adjustment')
                    ->label('Price Adj.')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray'))
                    ->formatStateUsing(fn ($state) => ($state > 0 ? '+' : '').'Rp '.number_format($state, 0, ',', '.'))
                    ->toggleable(),

                TextColumn::make('cost_adjustment')
                    ->label('Cost Adj.')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'warning' : ($state < 0 ? 'success' : 'gray'))
                    ->formatStateUsing(fn ($state) => ($state > 0 ? '+' : '').'Rp '.number_format($state, 0, ',', '.'))
                    ->toggleable(),

                TextColumn::make('barcode')
                    ->label('Barcode')
                    ->searchable()
                    ->copyable()
                    ->placeholder('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'out_of_stock' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'out_of_stock' => 'Out of Stock',
                        default => ucfirst($state),
                    })
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('product_id')
                    ->label('Product')
                    ->options(function () {
                        $business = Auth::user()->business;

                        return Product::query()
                            ->where('business_id', $business->id)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'out_of_stock' => 'Out of Stock',
                    ])
                    ->multiple(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }
}
