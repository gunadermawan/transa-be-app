<?php

namespace App\Filament\Resources\CashDrawerSessions\Pages;

use App\Filament\Resources\CashDrawerSessions\CashDrawerSessionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCashDrawerSessions extends ListRecords
{
    protected static string $resource = CashDrawerSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
