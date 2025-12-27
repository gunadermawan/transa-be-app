<?php

namespace App\Filament\Resources\BusinessSettings;

use App\Filament\Resources\BusinessSettings\Pages\CreateBusinessSetting;
use App\Filament\Resources\BusinessSettings\Pages\EditBusinessSetting;
use App\Filament\Resources\BusinessSettings\Pages\ListBusinessSettings;
use App\Filament\Resources\BusinessSettings\Schemas\BusinessSettingForm;
use App\Filament\Resources\BusinessSettings\Tables\BusinessSettingsTable;
use App\Models\BusinessSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class BusinessSettingResource extends Resource
{
    protected static ?string $model = BusinessSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Business Management';

    protected static ?int $navigationSort = 202;

    public static function form(Schema $schema): Schema
    {
        return BusinessSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BusinessSettingsTable::configure($table);
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
            'index' => ListBusinessSettings::route('/'),
            'create' => CreateBusinessSetting::route('/create'),
            'edit' => EditBusinessSetting::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // super_admin can see all business settings
        if ($user->role->name === 'super_admin') {
            return $query;
        }

        // business_owner can see settings for their business
        if ($user->role->name === 'business_owner') {
            return $query->where('business_id', $user->business_id);
        }

        // Others cannot access business settings
        return $query->whereRaw('1 = 0');
    }
}
