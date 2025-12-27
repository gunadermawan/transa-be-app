<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('role.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'business_owner' => 'success',
                        'manager' => 'warning',
                        'cashier' => 'info',
                        'staff' => 'gray',
                        default => 'primary',
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('business.name')
                    ->label('Business')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('outlet.name')
                    ->label('Outlet')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email_verified_at')
                    ->label('Verified At')
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
                SelectFilter::make('role')
                    ->relationship('role', 'name')
                    ->label('Role')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('business')
                    ->relationship('business', 'name')
                    ->label('Business')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('outlet')
                    ->relationship('outlet', 'name')
                    ->label('Outlet')
                    ->searchable()
                    ->preload(),
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
