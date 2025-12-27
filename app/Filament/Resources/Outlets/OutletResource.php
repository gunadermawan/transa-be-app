<?php

namespace App\Filament\Resources\Outlets;

use App\Filament\Resources\Outlets\Pages\CreateOutlet;
use App\Filament\Resources\Outlets\Pages\EditOutlet;
use App\Filament\Resources\Outlets\Pages\ListOutlets;
use App\Filament\Resources\Outlets\Schemas\OutletForm;
use App\Filament\Resources\Outlets\Tables\OutletsTable;
use App\Models\Outlet;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class OutletResource extends Resource
{
    protected static ?string $model = Outlet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|UnitEnum|null $navigationGroup = 'Business Management';

    protected static ?int $navigationSort = 201;

    public static function form(Schema $schema): Schema
    {
        return OutletForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OutletsTable::configure($table);
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
            'index' => ListOutlets::route('/'),
            'create' => CreateOutlet::route('/create'),
            'edit' => EditOutlet::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // super_admin can see all outlets
        if ($user->role->name === 'super_admin') {
            return $query;
        }

        // business_owner and manager can see outlets in their business
        if (in_array($user->role->name, ['business_owner', 'manager'])) {
            return $query->where('business_id', $user->business_id);
        }

        // Others cannot access outlets
        return $query->whereRaw('1 = 0');
    }
}
