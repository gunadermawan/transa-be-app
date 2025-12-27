<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->email ?: 'No email')
                    ->weight('bold'),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable()
                    ->placeholder('N/A')
                    ->icon('heroicon-o-phone'),

                TextColumn::make('customer_group')
                    ->label('Group')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'vip' => 'success',
                        'wholesale' => 'warning',
                        'member' => 'info',
                        'regular' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                TextColumn::make('total_spent')
                    ->label('Total Spent')
                    ->money('IDR')
                    ->sortable()
                    ->color('success')
                    ->weight('semibold')
                    ->toggleable(),

                TextColumn::make('loyalty_points')
                    ->label('Points')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->toggleable(),

                TextColumn::make('visit_count')
                    ->label('Visits')
                    ->numeric()
                    ->sortable()
                    ->description(fn ($record) => $record->last_visit_at ? 'Last: '.$record->last_visit_at->diffForHumans() : 'No visits')
                    ->toggleable(),

                TextColumn::make('birthdate')
                    ->label('Birth Date')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('address')
                    ->label('Address')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->address)
                    ->placeholder('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Deleted At')
                    ->dateTime()
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
                SelectFilter::make('customer_group')
                    ->label('Customer Group')
                    ->options([
                        'regular' => 'Regular',
                        'vip' => 'VIP',
                        'wholesale' => 'Wholesale',
                        'member' => 'Member',
                    ])
                    ->multiple(),

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
            ->defaultSort('created_at', 'desc');
    }
}
