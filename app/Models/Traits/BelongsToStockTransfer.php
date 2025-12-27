<?php

namespace App\Models\Traits;

use App\Models\Scopes\StockTransferScope;

trait BelongsToStockTransfer
{
    /**
     * Boot the BelongsToStockTransfer trait for a model.
     */
    protected static function bootBelongsToStockTransfer(): void
    {
        static::addGlobalScope(new StockTransferScope);
    }
}
