<?php

namespace App\Filament\Resources\SubscriptionPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('price')
                    ->money()
                    ->sortable(),
                TextColumn::make('billing_cycle')
                    ->searchable(),
                TextColumn::make('trial_days')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_outlets')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_users')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_products')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_transactions_per_month')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_popular')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
