<?php

namespace App\Filament\Resources\Businesses;

use App\Filament\Resources\Businesses\Pages\CreateBusiness;
use App\Filament\Resources\Businesses\Pages\EditBusiness;
use App\Filament\Resources\Businesses\Pages\ListBusinesses;
use App\Filament\Resources\Businesses\Pages\ViewBusiness;
use App\Filament\Resources\Businesses\Schemas\BusinessForm;
use App\Filament\Resources\Businesses\Tables\BusinessesTable;
use App\Models\Business;
use BackedEnum;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class BusinessResource extends Resource
{
    protected static ?string $model = Business::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Business Management';

    protected static ?int $navigationSort = 200;

    public static function form(Schema $schema): Schema
    {
        return BusinessForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BusinessesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Grid::make(3)
                    ->schema([
                        Section::make('Business Information')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Business Name')
                                    ->weight(FontWeight::Bold)
                                    ->size(TextSize::Large),
                                TextEntry::make('user.name')
                                    ->label('Business Owner')
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('outlets_count')
                                    ->label('Total Outlets')
                                    ->state(fn ($record) => $record->outlets()->count())
                                    ->icon('heroicon-o-building-storefront')
                                    ->badge()
                                    ->color('success'),
                            ])
                            ->columnSpan(2),

                        Section::make('Logo')
                            ->schema([
                                ImageEntry::make('logo')
                                    ->label('')
                                    ->defaultImageUrl(url('/images/default-business-logo.svg'))
                                    ->circular()
                                    ->height(150),
                            ])
                            ->columnSpan(1),
                    ]),

                Section::make('Contact Information')
                    ->schema([
                        TextEntry::make('address')
                            ->label('Address')
                            ->icon('heroicon-o-map-pin')
                            ->placeholder('No address provided')
                            ->columnSpanFull(),
                        TextEntry::make('phone')
                            ->label('Phone Number')
                            ->icon('heroicon-o-phone')
                            ->placeholder('No phone provided')
                            ->copyable(),
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-o-envelope')
                            ->placeholder('No email provided')
                            ->copyable(),
                        TextEntry::make('tax_id')
                            ->label('Tax ID / NPWP')
                            ->icon('heroicon-o-document-text')
                            ->placeholder('No tax ID provided')
                            ->copyable(),
                    ])
                    ->columns(3),

                Section::make('Subscription & Status')
                    ->schema([
                        TextEntry::make('subscription_status')
                            ->label('Subscription Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'trial' => 'info',
                                'active' => 'success',
                                'past_due' => 'warning',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('status')
                            ->label('Business Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'active' => 'success',
                                'suspended' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('activated_at')
                            ->label('Activated Date')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-check-circle')
                            ->placeholder('Not activated yet'),
                        TextEntry::make('expired_at')
                            ->label('Expiration Date')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-clock')
                            ->placeholder('No expiration set')
                            ->color(fn ($state) => $state && $state < now() ? 'danger' : 'success'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('System Information')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-clock')
                            ->since(),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBusinesses::route('/'),
            'create' => CreateBusiness::route('/create'),
            'view' => ViewBusiness::route('/{record}'),
            'edit' => EditBusiness::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // super_admin can see all businesses
        if ($user->role->name === 'super_admin') {
            return $query;
        }

        // business_owner can only see their own business
        if ($user->role->name === 'business_owner') {
            return $query->where('id', $user->business_id);
        }

        // Others cannot access businesses
        return $query->whereRaw('1 = 0');
    }
}
