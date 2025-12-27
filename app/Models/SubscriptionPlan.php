<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'billing_cycle',
        'trial_days',
        'max_outlets',
        'max_users',
        'max_products',
        'max_transactions_per_month',
        'features',
        'is_popular',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_popular' => 'boolean',
        ];
    }

    /**
     * Get the subscriptions for this plan.
     */
    public function subscriptions()
    {
        return $this->hasMany(BusinessSubscription::class, 'plan_id');
    }
}
