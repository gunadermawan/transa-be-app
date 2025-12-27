<?php

namespace App\Filament\Resources\Vouchers;

use App\Filament\Resources\Vouchers\Pages\ComingSoon;
use App\Filament\Resources\Vouchers\Schemas\VoucherForm;
use App\Filament\Resources\Vouchers\Tables\VouchersTable;
use App\Models\Voucher;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static string|UnitEnum|null $navigationGroup = 'Marketing & Promotions';

    protected static ?int $navigationSort = 601;

    public static function form(Schema $schema): Schema
    {
        return VoucherForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VouchersTable::configure($table);
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

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
