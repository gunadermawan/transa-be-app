<?php

namespace App\Filament\Resources\CashTransactions;

use App\Filament\Resources\CashTransactions\Pages\ComingSoon;
use App\Filament\Resources\CashTransactions\Schemas\CashTransactionForm;
use App\Filament\Resources\CashTransactions\Tables\CashTransactionsTable;
use App\Models\CashTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CashTransactionResource extends Resource
{
    protected static ?string $model = CashTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsUpDown;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Orders';

    protected static ?int $navigationSort = 504;

    public static function form(Schema $schema): Schema
    {
        return CashTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CashTransactionsTable::configure($table);
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
