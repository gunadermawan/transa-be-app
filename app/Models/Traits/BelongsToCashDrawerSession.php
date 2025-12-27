<?php

namespace App\Models\Traits;

use App\Models\Scopes\CashDrawerSessionScope;

trait BelongsToCashDrawerSession
{
    /**
     * Boot the BelongsToCashDrawerSession trait for a model.
     */
    protected static function bootBelongsToCashDrawerSession(): void
    {
        static::addGlobalScope(new CashDrawerSessionScope);
    }
}
