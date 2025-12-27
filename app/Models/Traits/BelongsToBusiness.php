<?php

namespace App\Models\Traits;

use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Model;

trait BelongsToBusiness
{
    /**
     * Boot the BelongsToBusiness trait for a model.
     */
    protected static function bootBelongsToBusiness(): void
    {
        static::addGlobalScope(new BusinessScope);

        static::creating(function (Model $model) {
            if (! $model->business_id && auth()->check() && auth()->user()->business_id) {
                $model->business_id = auth()->user()->business_id;
            }
        });
    }
}
