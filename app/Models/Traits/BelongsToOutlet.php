<?php

namespace App\Models\Traits;

use App\Models\Scopes\OutletScope;
use Illuminate\Database\Eloquent\Model;

trait BelongsToOutlet
{
    /**
     * Boot the BelongsToOutlet trait for a model.
     */
    protected static function bootBelongsToOutlet(): void
    {
        static::addGlobalScope(new OutletScope);

        static::creating(function (Model $model) {
            if (! $model->outlet_id && auth()->check() && auth()->user()->outlet_id) {
                $model->outlet_id = auth()->user()->outlet_id;
            }
        });
    }
}
