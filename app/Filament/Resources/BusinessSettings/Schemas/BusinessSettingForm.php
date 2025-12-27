<?php

namespace App\Filament\Resources\BusinessSettings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BusinessSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Setting Information')
                    ->schema([
                        Select::make('business_id')
                            ->label('Business')
                            ->relationship('business', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(function () {
                                $currentUser = auth()->user();
                                // business_owner can only create settings for their business
                                if ($currentUser->role->name === 'business_owner') {
                                    return $currentUser->business_id;
                                }

                                return null;
                            })
                            ->disabled(fn () => auth()->user()->role->name === 'business_owner')
                            ->dehydrated(),
                        TextInput::make('name')
                            ->label('Setting Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., PPN 11%'),
                    ])
                    ->columns(1),

                Section::make('Setting Details')
                    ->description('Configure tax and service charges for this business. For discounts and promotions, use the Promotions feature.')
                    ->schema([
                        Select::make('type')
                            ->label('Setting Type')
                            ->options([
                                'tax' => 'Tax (Pajak)',
                                'service' => 'Service Charge (Biaya Layanan)',
                            ])
                            ->required()
                            ->helperText('Select tax for VAT/PPN or service for service charges'),
                        Select::make('charge_type')
                            ->label('Charge Type')
                            ->options([
                                'percentage' => 'Percentage (%)',
                                'fixed' => 'Fixed Amount (Rp)',
                            ])
                            ->required()
                            ->helperText('Percentage is relative to total, fixed is absolute amount'),
                        TextInput::make('value')
                            ->label('Value')
                            ->required()
                            ->numeric()
                            ->step('0.01')
                            ->suffix(fn ($get) => $get('charge_type') === 'percentage' ? '%' : 'Rp')
                            ->helperText('Enter value: 11 for 11% or 5000 for Rp 5,000'),
                    ])
                    ->columns(1),
            ]);
    }
}
