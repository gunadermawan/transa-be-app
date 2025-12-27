<?php

namespace App\Filament\Resources\StockTransfers\Tables;

use App\Models\Outlet;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class StockTransfersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transfer_number')
                    ->label('Transfer #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('fromOutlet.name')
                    ->label('From Outlet')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->fromOutlet?->address),

                TextColumn::make('toOutlet.name')
                    ->label('To Outlet')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->toOutlet?->address),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'approved' => 'info',
                        'sent' => 'warning',
                        'received' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('transfer_date')
                    ->label('Transfer Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('requestedByUser.name')
                    ->label('Requested By')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->suffix(' items')
                    ->sortable()
                    ->toggleable(),

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
                SelectFilter::make('from_outlet_id')
                    ->label('From Outlet')
                    ->options(function () {
                        $business = Auth::user()->business;

                        return Outlet::query()
                            ->where('business_id', $business->id)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make('to_outlet_id')
                    ->label('To Outlet')
                    ->options(function () {
                        $business = Auth::user()->business;

                        return Outlet::query()
                            ->where('business_id', $business->id)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'sent' => 'Sent',
                        'received' => 'Received',
                        'cancelled' => 'Cancelled',
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
            ->defaultSort('created_at', 'desc');
    }
}
