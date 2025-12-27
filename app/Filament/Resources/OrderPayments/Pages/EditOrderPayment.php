<?php

namespace App\Filament\Resources\OrderPayments\Pages;

use App\Filament\Resources\OrderPayments\OrderPaymentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrderPayment extends EditRecord
{
    protected static string $resource = OrderPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
