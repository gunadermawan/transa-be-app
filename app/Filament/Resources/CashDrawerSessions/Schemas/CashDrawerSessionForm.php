<?php

namespace App\Filament\Resources\CashDrawerSessions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CashDrawerSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('outlet_id')
                    ->relationship('outlet', 'name')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('session_number')
                    ->required(),
                TextInput::make('opening_balance')
                    ->required()
                    ->numeric(),
                TextInput::make('closing_balance')
                    ->numeric()
                    ->default(null),
                TextInput::make('expected_cash')
                    ->numeric()
                    ->default(null),
                TextInput::make('actual_cash')
                    ->numeric()
                    ->default(null),
                TextInput::make('difference')
                    ->numeric()
                    ->default(null),
                DateTimePicker::make('opened_at')
                    ->required(),
                DateTimePicker::make('closed_at'),
                TextInput::make('status')
                    ->required()
                    ->default('open'),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
