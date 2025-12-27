<?php

namespace App\Models;

use App\Models\Traits\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use BelongsToBusiness, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'phone',
        'email',
        'address',
        'loyalty_points',
        'total_spent',
        'visit_count',
        'last_visit_at',
        'birthdate',
        'customer_group',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'loyalty_points' => 'integer',
            'total_spent' => 'decimal:2',
            'visit_count' => 'integer',
            'last_visit_at' => 'datetime',
            'birthdate' => 'date',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
