<?php

namespace App\Filament\Resources\Outlets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OutletForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Outlet Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Outlet Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Cabang Jakarta Selatan'),
                        Select::make('business_id')
                            ->label('Business')
                            ->relationship('business', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(function () {
                                $currentUser = auth()->user();
                                // business_owner can only create outlets for their business
                                if ($currentUser->role->name === 'business_owner') {
                                    return $currentUser->business_id;
                                }

                                return null;
                            })
                            ->disabled(fn () => auth()->user()->role->name === 'business_owner')
                            ->dehydrated(),
                    ])
                    ->columns(1),

                Section::make('Contact & Location')
                    ->schema([
                        Textarea::make('address')
                            ->label('Address')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+62 xxx xxxx xxxx'),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
