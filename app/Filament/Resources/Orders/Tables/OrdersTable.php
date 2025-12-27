<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->description(fn ($record) => $record->created_at->format('d M Y, H:i')),

                TextColumn::make('outlet.name')
                    ->label('Outlet')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->placeholder('Walk-in')
                    ->description(fn ($record) => $record->customer?->phone)
                    ->toggleable(),

                TextColumn::make('total_items')
                    ->label('Items')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->weight('semibold')
                    ->color('success'),

                TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'debit_card', 'credit_card' => 'info',
                        'e_wallet', 'qris' => 'warning',
                        'bank_transfer' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Cash',
                        'debit_card' => 'Debit',
                        'credit_card' => 'Credit',
                        'e_wallet' => 'E-Wallet',
                        'bank_transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        default => ucfirst($state),
                    })
                    ->sortable(),

                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Order Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'processing' => 'info',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                TextColumn::make('cashier.name')
                    ->label('Cashier')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('sub_total')
                    ->label('Subtotal')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tax')
                    ->label('Tax')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('discount')
                    ->label('Discount')
                    ->money('IDR')
                    ->sortable()
                    ->color('danger')
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
                SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ])
                    ->multiple(),

                SelectFilter::make('status')
                    ->label('Order Status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),

                SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'cash' => 'Cash',
                        'debit_card' => 'Debit Card',
                        'credit_card' => 'Credit Card',
                        'e_wallet' => 'E-Wallet',
                        'bank_transfer' => 'Bank Transfer',
                        'qris' => 'QRIS',
                    ])
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
