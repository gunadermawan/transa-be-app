<?php

namespace App\Filament\Resources\Vouchers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('business_id')
                    ->relationship('business', 'name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('discount_type')
                    ->required(),
                TextInput::make('discount_value')
                    ->required()
                    ->numeric(),
                TextInput::make('min_purchase_amount')
                    ->numeric()
                    ->default(null),
                TextInput::make('max_discount_amount')
                    ->numeric()
                    ->default(null),
                TextInput::make('usage_limit')
                    ->numeric()
                    ->default(null),
                TextInput::make('usage_limit_per_customer')
                    ->numeric()
                    ->default(null),
                TextInput::make('used_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                DatePicker::make('valid_from')
                    ->required(),
                DatePicker::make('valid_until')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
            ]);
    }
}
