<?php

namespace App\Models\Traits;

use App\Models\Scopes\SubscriptionInvoiceScope;

trait BelongsToSubscriptionInvoice
{
    /**
     * Boot the BelongsToSubscriptionInvoice trait for a model.
     */
    protected static function bootBelongsToSubscriptionInvoice(): void
    {
        static::addGlobalScope(new SubscriptionInvoiceScope);
    }
}
