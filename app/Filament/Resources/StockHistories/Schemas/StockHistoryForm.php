<?php

namespace App\Filament\Resources\StockHistories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StockHistoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('stock_id')
                    ->required()
                    ->numeric(),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('outlet_id')
                    ->required()
                    ->numeric(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                TextInput::make('current_stock')
                    ->required()
                    ->numeric(),
                TextInput::make('type')
                    ->required(),
                TextInput::make('reference')
                    ->required(),
                TextInput::make('note')
                    ->default(null),
            ]);
    }
}
