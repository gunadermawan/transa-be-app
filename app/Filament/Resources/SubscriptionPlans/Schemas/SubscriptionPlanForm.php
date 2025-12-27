<?php

namespace App\Filament\Resources\SubscriptionPlans\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SubscriptionPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('billing_cycle')
                    ->required()
                    ->default('monthly'),
                TextInput::make('trial_days')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('max_outlets')
                    ->numeric()
                    ->default(null),
                TextInput::make('max_users')
                    ->numeric()
                    ->default(null),
                TextInput::make('max_products')
                    ->numeric()
                    ->default(null),
                TextInput::make('max_transactions_per_month')
                    ->numeric()
                    ->default(null),
                Textarea::make('features')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('is_popular')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
            ]);
    }
}
