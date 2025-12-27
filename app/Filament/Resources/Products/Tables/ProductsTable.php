<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')
                    ->label('')
                    ->default('#e5e7eb')
                    ->visible(fn ($record) => $record && ! $record->image),

                ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->visible(fn ($record) => $record && $record->image),

                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-m-cube')
                    ->color('primary')
                    ->description(fn ($record) => $record->sku),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('price')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->color('success'),

                TextColumn::make('cost')
                    ->label('Harga Modal')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('unit_type')
                    ->label('Satuan')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'out_of_stock' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'out_of_stock' => 'Stok Habis',
                        default => $state,
                    })
                    ->sortable(),

                IconColumn::make('is_stock_managed')
                    ->label('Kelola Stok')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),

                TextColumn::make('stock_minimum')
                    ->label('Stok Min')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->suffix(' unit'),

                IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('barcode')
                    ->label('Barcode')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Barcode disalin!')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('SKU disalin!')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('product_type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'physical' => 'Fisik',
                        'digital' => 'Digital',
                        'service' => 'Jasa',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'physical' => 'success',
                        'digital' => 'info',
                        'service' => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Dihapus')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'out_of_stock' => 'Stok Habis',
                    ]),

                SelectFilter::make('product_type')
                    ->label('Tipe Produk')
                    ->options([
                        'physical' => 'Produk Fisik',
                        'digital' => 'Produk Digital',
                        'service' => 'Jasa/Layanan',
                    ]),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'asc')
            ->striped()
            ->emptyStateHeading('Belum ada produk')
            ->emptyStateDescription('Mulai tambahkan produk untuk dijual di toko Anda.')
            ->emptyStateIcon('heroicon-o-cube');
    }
}
