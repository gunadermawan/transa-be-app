<?php

namespace App\Filament\Resources\StockHistories;

use App\Filament\Resources\StockHistories\Pages\ListStockHistories;
use App\Filament\Resources\StockHistories\Schemas\StockHistoryForm;
use App\Filament\Resources\StockHistories\Tables\StockHistoriesTable;
use App\Models\StockHistory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StockHistoryResource extends Resource
{
    protected static ?string $model = StockHistory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = 'Inventory Management';

    protected static ?int $navigationSort = 404;

    protected static ?string $navigationLabel = 'Stock Histories';

    protected static ?string $modelLabel = 'Stock History';

    protected static ?string $pluralModelLabel = 'Stock Histories';

    public static function form(Schema $schema): Schema
    {
        return StockHistoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockHistoriesTable::configure($table);
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
            'index' => ListStockHistories::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
