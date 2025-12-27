<?php

namespace App\Filament\Resources\OrderPayments\Pages;

use App\Filament\Resources\OrderPayments\OrderPaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderPayment extends CreateRecord
{
    protected static string $resource = OrderPaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
