<?php

namespace App\Filament\Resources\OrderPayments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderPaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->description(fn ($record) => $record->order->customer?->name ?? 'Walk-in'),

                TextColumn::make('payment_method')
                    ->label('Payment Method')
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
                        'debit_card' => 'Debit Card',
                        'credit_card' => 'Credit Card',
                        'e_wallet' => 'E-Wallet',
                        'bank_transfer' => 'Bank Transfer',
                        'qris' => 'QRIS',
                        default => ucfirst($state),
                    })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('IDR')
                    ->sortable()
                    ->weight('semibold')
                    ->color('success'),

                TextColumn::make('reference_number')
                    ->label('Reference #')
                    ->searchable()
                    ->placeholder('—')
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                TextColumn::make('order.total_price')
                    ->label('Order Total')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('order.payment_status')
                    ->label('Order Payment Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Payment Date')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->description(fn ($record) => $record->created_at->format('d M Y, H:i')),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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

                SelectFilter::make('status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
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
