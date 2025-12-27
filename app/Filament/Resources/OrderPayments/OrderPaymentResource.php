<?php

namespace App\Filament\Resources\OrderPayments;

use App\Filament\Resources\OrderPayments\Pages\CreateOrderPayment;
use App\Filament\Resources\OrderPayments\Pages\EditOrderPayment;
use App\Filament\Resources\OrderPayments\Pages\ListOrderPayments;
use App\Filament\Resources\OrderPayments\Schemas\OrderPaymentForm;
use App\Filament\Resources\OrderPayments\Tables\OrderPaymentsTable;
use App\Models\OrderPayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OrderPaymentResource extends Resource
{
    protected static ?string $model = OrderPayment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Orders';

    protected static ?int $navigationSort = 502;

    protected static ?string $navigationLabel = 'Order Payments';

    protected static ?string $modelLabel = 'Order Payment';

    protected static ?string $pluralModelLabel = 'Order Payments';

    public static function form(Schema $schema): Schema
    {
        return OrderPaymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrderPaymentsTable::configure($table);
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
            'index' => ListOrderPayments::route('/'),
            'create' => CreateOrderPayment::route('/create'),
            'edit' => EditOrderPayment::route('/{record}/edit'),
        ];
    }
}
