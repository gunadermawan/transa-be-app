<?php

namespace App\Filament\Resources\Stocks\Pages;

use App\Filament\Resources\Stocks\StockResource;
use App\Models\StockHistory;
use Filament\Resources\Pages\CreateRecord;

class CreateStock extends CreateRecord
{
    protected static string $resource = StockResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $stock = $this->record;

        // Create stock history for initial stock
        StockHistory::create([
            'stock_id' => $stock->id,
            'user_id' => auth()->id(),
            'outlet_id' => $stock->outlet_id,
            'quantity' => $stock->quantity,
            'current_stock' => $stock->quantity,
            'type' => 'in',
            'reference' => 'Initial Stock',
            'note' => 'Initial stock entry from admin panel',
        ]);
    }
}
