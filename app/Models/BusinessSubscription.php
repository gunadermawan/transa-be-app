<?php

namespace App\Models;

use App\Models\Traits\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;

class BusinessSubscription extends Model
{
    use BelongsToBusiness;

    protected $fillable = [
        'business_id',
        'subscription_plan_id',
        'start_date',
        'end_date',
        'trial_ends_at',
        'next_billing_date',
        'status',
        'auto_renew',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'trial_ends_at' => 'date',
            'next_billing_date' => 'date',
            'cancelled_at' => 'datetime',
            'auto_renew' => 'boolean',
        ];
    }

    /**
     * Get the business that owns the subscription.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the plan for this subscription.
     */
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
