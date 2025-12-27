<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrderScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check() && auth()->user()->business_id) {
            $builder->whereHas('order', function ($query) {
                $query->whereHas('outlet', function ($q) {
                    $q->where('business_id', auth()->user()->business_id);
                });
            });
        }
    }
}
