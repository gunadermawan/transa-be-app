<?php

namespace App\Models\Traits;

use App\Models\Scopes\PurchaseOrderScope;

trait BelongsToPurchaseOrder
{
    /**
     * Boot the BelongsToPurchaseOrder trait for a model.
     */
    protected static function bootBelongsToPurchaseOrder(): void
    {
        static::addGlobalScope(new PurchaseOrderScope);
    }
}
