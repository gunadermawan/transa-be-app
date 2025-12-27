<?php

namespace App\Filament\Resources\Stocks\Pages;

use App\Filament\Resources\Stocks\StockResource;
use App\Models\StockHistory;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStock extends EditRecord
{
    protected static string $resource = StockResource::class;

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

    protected function afterSave(): void
    {
        $stock = $this->record;
        $originalQuantity = $this->record->getOriginal('quantity');
        $newQuantity = $stock->quantity;

        // Only create history if quantity changed
        if ($originalQuantity != $newQuantity) {
            $quantityDiff = $newQuantity - $originalQuantity;
            $type = $quantityDiff > 0 ? 'in' : 'out';

            StockHistory::create([
                'stock_id' => $stock->id,
                'user_id' => auth()->id(),
                'outlet_id' => $stock->outlet_id,
                'quantity' => abs($quantityDiff),
                'current_stock' => $newQuantity,
                'type' => 'adjustment',
                'reference' => 'Stock Adjustment',
                'note' => "Stock adjusted from {$originalQuantity} to {$newQuantity} units via admin panel",
            ]);
        }
    }
}
