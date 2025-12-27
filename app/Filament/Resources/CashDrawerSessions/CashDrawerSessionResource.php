<?php

namespace App\Filament\Resources\CashDrawerSessions;

use App\Filament\Resources\CashDrawerSessions\Pages\ComingSoon;
use App\Filament\Resources\CashDrawerSessions\Schemas\CashDrawerSessionForm;
use App\Filament\Resources\CashDrawerSessions\Tables\CashDrawerSessionsTable;
use App\Models\CashDrawerSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CashDrawerSessionResource extends Resource
{
    protected static ?string $model = CashDrawerSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Orders';

    protected static ?int $navigationSort = 503;

    public static function form(Schema $schema): Schema
    {
        return CashDrawerSessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CashDrawerSessionsTable::configure($table);
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
            'index' => ComingSoon::route('/'),
        ];
    }
}
