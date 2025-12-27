<?php

namespace App\Filament\Resources\Suppliers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->copyMessage('Kode supplier berhasil disalin!')
                    ->icon('heroicon-m-hashtag')
                    ->color('primary'),

                TextColumn::make('name')
                    ->label('Nama Supplier')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->description(fn ($record) => $record->contact_person ? 'PIC: '.$record->contact_person : null)
                    ->icon('heroicon-m-building-storefront'),

                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->icon('heroicon-m-phone')
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('payment_terms')
                    ->label('Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'tempo_7' => 'info',
                        'tempo_14' => 'warning',
                        'tempo_30' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Tunai',
                        'tempo_7' => 'Tempo 7 Hari',
                        'tempo_14' => 'Tempo 14 Hari',
                        'tempo_30' => 'Tempo 30 Hari',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'blocked' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),

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
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'blocked' => 'Blocked',
                    ])
                    ->native(false),

                SelectFilter::make('payment_terms')
                    ->label('Ketentuan Pembayaran')
                    ->options([
                        'cash' => 'Tunai/Cash',
                        'tempo_7' => 'Tempo 7 Hari',
                        'tempo_14' => 'Tempo 14 Hari',
                        'tempo_30' => 'Tempo 30 Hari',
                    ])
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
            ->emptyStateHeading('Belum ada supplier')
            ->emptyStateDescription('Mulai tambahkan supplier untuk mengelola pembelian produk.')
            ->emptyStateIcon('heroicon-o-building-storefront');
    }
}
