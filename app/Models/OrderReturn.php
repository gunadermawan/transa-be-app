<?php

namespace App\Models;

use App\Models\Traits\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderReturn extends Model
{
    use BelongsToBusiness, SoftDeletes;

    protected $fillable = [
        'order_id',
        'business_id',
        'outlet_id',
        'cashier_id',
        'return_number',
        'return_date',
        'total_refund',
        'refund_method',
        'reason',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'return_date' => 'date',
            'total_refund' => 'decimal:2',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}
