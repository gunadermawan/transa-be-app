<?php

namespace App\Filament\Resources\Businesses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BusinessesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-business-logo.svg'))
                    ->toggleable(),
                TextColumn::make('name')
                    ->label('Business Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'active' => 'success',
                        'suspended' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('subscription_status')
                    ->label('Subscription')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'trial' => 'info',
                        'active' => 'success',
                        'past_due' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('outlets_count')
                    ->label('Outlets')
                    ->counts('outlets')
                    ->sortable(),
                TextColumn::make('products_count')
                    ->label('Products')
                    ->counts('products')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('customers_count')
                    ->label('Customers')
                    ->counts('customers')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('activated_at')
                    ->label('Activated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('expired_at')
                    ->label('Expires')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                    ])
                    ->multiple(),
                SelectFilter::make('subscription_status')
                    ->label('Subscription')
                    ->options([
                        'trial' => 'Trial',
                        'active' => 'Active',
                        'past_due' => 'Past Due',
                        'cancelled' => 'Cancelled',
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
            ]);
    }
}
