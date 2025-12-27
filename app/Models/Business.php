<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = [
        'name',
        'owner_id',
        'address',
        'phone',
        'email',
        'tax_id',
        'logo',
        'current_subscription_id',
        'subscription_status',
        'status',
        'activated_at',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'activated_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the business.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the current subscription.
     */
    public function currentSubscription()
    {
        return $this->belongsTo(BusinessSubscription::class, 'current_subscription_id');
    }

    /**
     * Get all subscriptions.
     */
    public function subscriptions()
    {
        return $this->hasMany(BusinessSubscription::class);
    }

    /**
     * Get the outlets for the business.
     */
    public function outlets()
    {
        return $this->hasMany(Outlet::class);
    }

    /**
     * Get the customers for the business.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Get the products for the business.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the settings for the business.
     */
    public function settings()
    {
        return $this->hasMany(BusinessSetting::class);
    }
}
