<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Information')
                    ->description('Basic information about the customer')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->placeholder('John Doe')
                            ->helperText('Customer full name'),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->placeholder('+62 812-3456-7890')
                            ->helperText('Customer phone number (optional)'),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->placeholder('customer@example.com')
                            ->helperText('Customer email address (optional)'),

                        Select::make('customer_group')
                            ->label('Customer Group')
                            ->required()
                            ->options([
                                'regular' => 'Regular',
                                'vip' => 'VIP',
                                'wholesale' => 'Wholesale',
                                'member' => 'Member',
                            ])
                            ->default('regular')
                            ->native(false)
                            ->helperText('Customer category or membership level'),

                        DatePicker::make('birthdate')
                            ->label('Birth Date')
                            ->placeholder('YYYY-MM-DD')
                            ->native(false)
                            ->helperText('Customer birth date for birthday promotions'),
                    ])
                    ->columns(2),

                Section::make('Address & Notes')
                    ->description('Additional customer details')
                    ->schema([
                        Textarea::make('address')
                            ->label('Address')
                            ->rows(3)
                            ->placeholder('Complete customer address')
                            ->helperText('Customer full address (optional)'),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->placeholder('Additional notes about this customer')
                            ->helperText('Internal notes (optional)'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
