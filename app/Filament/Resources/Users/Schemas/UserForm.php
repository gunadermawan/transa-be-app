<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                    ])
                    ->columns(1),

                Section::make('Access & Assignment')
                    ->schema([
                        Select::make('role_id')
                            ->label('Role')
                            ->relationship(
                                name: 'role',
                                titleAttribute: 'name',
                                modifyQueryUsing: function ($query) {
                                    $currentUser = auth()->user();

                                    // business_owner can only assign manager, cashier, staff
                                    if ($currentUser->role->name === 'business_owner') {
                                        return $query->whereIn('name', ['manager', 'cashier', 'staff']);
                                    }

                                    // super_admin can assign any role
                                    return $query;
                                }
                            )
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('business_id')
                            ->label('Business')
                            ->relationship('business', 'name')
                            ->searchable()
                            ->preload()
                            ->default(function () {
                                $currentUser = auth()->user();
                                // business_owner can only assign users to their business
                                if ($currentUser->role->name === 'business_owner') {
                                    return $currentUser->business_id;
                                }

                                return null;
                            })
                            ->disabled(fn () => auth()->user()->role->name === 'business_owner')
                            ->dehydrated(),
                        Select::make('outlet_id')
                            ->label('Outlet')
                            ->relationship(
                                name: 'outlet',
                                titleAttribute: 'name',
                                modifyQueryUsing: function ($query, $get) {
                                    $currentUser = auth()->user();
                                    $businessId = $get('business_id') ?? $currentUser->business_id;

                                    // Filter outlets by selected business or current user's business
                                    if ($businessId) {
                                        return $query->where('business_id', $businessId);
                                    }

                                    return $query;
                                }
                            )
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(1),

                Section::make('Security')
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255)
                            ->label('Password')
                            ->helperText('Leave blank to keep current password (when editing)'),
                    ]),
            ]);
    }
}
