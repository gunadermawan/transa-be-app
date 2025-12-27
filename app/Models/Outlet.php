<?php

namespace App\Models;

use App\Models\Traits\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use BelongsToBusiness;

    protected $fillable = ['name', 'business_id', 'address', 'phone', 'description'];

    /**
     * Get the business that owns the outlet.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the orders for the outlet.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the users assigned to this outlet.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the stocks for this outlet.
     */
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
