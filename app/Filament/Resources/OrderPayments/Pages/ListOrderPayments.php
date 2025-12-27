<?php

namespace App\Filament\Resources\OrderPayments\Pages;

use App\Filament\Resources\OrderPayments\OrderPaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrderPayments extends ListRecords
{
    protected static string $resource = OrderPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
