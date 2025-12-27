<?php

namespace App\Filament\Resources\CashDrawerSessions\Pages;

use App\Filament\Resources\CashDrawerSessions\CashDrawerSessionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCashDrawerSession extends EditRecord
{
    protected static string $resource = CashDrawerSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
