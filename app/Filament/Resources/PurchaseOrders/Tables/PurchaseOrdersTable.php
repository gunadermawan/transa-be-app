<?php

namespace App\Filament\Resources\PurchaseOrders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PurchaseOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('po_number')
                    ->label('Nomor PO')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->copyMessage('Nomor PO berhasil disalin!')
                    ->icon('heroicon-m-hashtag')
                    ->color('primary'),

                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-building-storefront')
                    ->weight(FontWeight::SemiBold),

                TextColumn::make('outlet.name')
                    ->label('Outlet')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-map-pin')
                    ->toggleable(),

                TextColumn::make('order_date')
                    ->label('Tgl Order')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar'),

                TextColumn::make('expected_date')
                    ->label('Estimasi Tiba')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-m-clock')
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending' => 'warning',
                        'approved' => 'info',
                        'received' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'received' => 'Received',
                        'cancelled' => 'Cancelled',
                        default => ucfirst($state),
                    })
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                TextColumn::make('reference')
                    ->label('Referensi')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('received_date')
                    ->label('Tgl Diterima')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('receiver.name')
                    ->label('Diterima Oleh')
                    ->searchable()
                    ->placeholder('—')
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
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'received' => 'Received',
                        'cancelled' => 'Cancelled',
                    ])
                    ->native(false),

                SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('outlet_id')
                    ->label('Outlet')
                    ->relationship('outlet', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('Belum ada purchase order')
            ->emptyStateDescription('Mulai buat purchase order untuk order barang dari supplier.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }
}
