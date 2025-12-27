<?php

namespace App\Models\Traits;

use App\Models\Scopes\OrderScope;

trait BelongsToOrder
{
    /**
     * Boot the BelongsToOrder trait for a model.
     */
    protected static function bootBelongsToOrder(): void
    {
        static::addGlobalScope(new OrderScope);
    }
}
