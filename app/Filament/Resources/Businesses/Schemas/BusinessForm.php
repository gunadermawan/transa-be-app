<?php

namespace App\Filament\Resources\Businesses\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BusinessForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Business Information')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Business Name')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('e.g., Toko Maju Jaya'),
                                Select::make('owner_id')
                                    ->label('Business Owner')
                                    ->relationship('user', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Select the user who will own this business')
                                    ->disabled(fn () => auth()->user()->role->name !== 'super_admin')
                                    ->dehydrated(),
                            ])
                            ->columnSpan(2),

                        Section::make('Logo')
                            ->schema([
                                FileUpload::make('logo')
                                    ->label('')
                                    ->image()
                                    ->directory('business-logos')
                                    ->maxSize(2048)
                                    ->imageEditor()
                                    ->avatar()
                                    ->imagePreviewHeight('150'),
                            ])
                            ->columnSpan(1),
                    ]),

                Section::make('Contact Information')
                    ->schema([
                        Textarea::make('address')
                            ->label('Business Address')
                            ->rows(3)
                            ->maxLength(500),
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+62 xxx xxxx xxxx'),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('contact@business.com'),
                        TextInput::make('tax_id')
                            ->label('Tax ID / NPWP')
                            ->maxLength(50)
                            ->placeholder('xx.xxx.xxx.x-xxx.xxx'),
                    ])
                    ->columns(1),

                Section::make('Subscription & Status')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('subscription_status')
                                    ->label('Subscription Status')
                                    ->options([
                                        'trial' => 'Trial',
                                        'active' => 'Active',
                                        'past_due' => 'Past Due',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('trial')
                                    ->required()
                                    ->disabled(fn () => auth()->user()->role->name !== 'super_admin')
                                    ->dehydrated(fn () => auth()->user()->role->name === 'super_admin')
                                    ->helperText(fn () => auth()->user()->role->name !== 'super_admin' ? 'Contact admin to change subscription status' : null),
                                Select::make('status')
                                    ->label('Business Status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'active' => 'Active',
                                        'suspended' => 'Suspended',
                                    ])
                                    ->default('pending')
                                    ->required()
                                    ->disabled(fn () => auth()->user()->role->name !== 'super_admin')
                                    ->dehydrated(fn () => auth()->user()->role->name === 'super_admin')
                                    ->helperText(fn () => auth()->user()->role->name !== 'super_admin' ? 'Contact admin to change business status' : null),
                            ]),
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('activated_at')
                                    ->label('Activated Date')
                                    ->disabled(fn () => auth()->user()->role->name !== 'super_admin')
                                    ->dehydrated(fn () => auth()->user()->role->name === 'super_admin')
                                    ->helperText(fn () => auth()->user()->role->name !== 'super_admin' ? 'Contact admin to modify' : 'Date when business was activated'),
                                DateTimePicker::make('expired_at')
                                    ->label('Expiration Date')
                                    ->disabled(fn () => auth()->user()->role->name !== 'super_admin')
                                    ->dehydrated(fn () => auth()->user()->role->name === 'super_admin')
                                    ->helperText(fn () => auth()->user()->role->name !== 'super_admin' ? 'Contact admin to extend subscription' : 'Date when subscription expires'),
                            ]),
                    ])
                    ->collapsible()
                    ->description(fn () => auth()->user()->role->name !== 'super_admin' ? 'View only - Contact administrator to modify subscription settings' : null),
            ]);
    }
}
