<?php

namespace App\Models\Traits;

use App\Models\Scopes\ProductScope;

trait BelongsToProduct
{
    /**
     * Boot the BelongsToProduct trait for a model.
     */
    protected static function bootBelongsToProduct(): void
    {
        static::addGlobalScope(new ProductScope);
    }
}
