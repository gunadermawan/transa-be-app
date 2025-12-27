<?php

namespace App\Filament\Resources\BusinessSettings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BusinessSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('business.name')
                    ->label('Business')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Setting Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'tax' => 'Tax',
                        'service' => 'Service Charge',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'tax' => 'success',
                        'service' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('charge_type')
                    ->label('Charge Type')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount',
                        default => $state,
                    })
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('value')
                    ->label('Value')
                    ->formatStateUsing(function ($record, $state) {
                        if ($record->charge_type === 'percentage') {
                            return $state.'%';
                        }

                        return 'Rp '.number_format($state, 0, ',', '.');
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('business')
                    ->relationship('business', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('type')
                    ->options([
                        'tax' => 'Tax',
                        'service' => 'Service Charge',
                    ])
                    ->multiple(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
