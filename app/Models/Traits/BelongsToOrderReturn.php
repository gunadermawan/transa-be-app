<?php

namespace App\Models\Traits;

use App\Models\Scopes\OrderReturnScope;

trait BelongsToOrderReturn
{
    /**
     * Boot the BelongsToOrderReturn trait for a model.
     */
    protected static function bootBelongsToOrderReturn(): void
    {
        static::addGlobalScope(new OrderReturnScope);
    }
}
