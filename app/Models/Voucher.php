<?php

namespace App\Models;

use App\Models\Traits\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use BelongsToBusiness, SoftDeletes;

    protected $fillable = [
        'business_id',
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'min_purchase_amount',
        'max_discount_amount',
        'usage_limit',
        'usage_limit_per_customer',
        'used_count',
        'valid_from',
        'valid_until',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_purchase_amount' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'usage_limit' => 'integer',
            'usage_limit_per_customer' => 'integer',
            'used_count' => 'integer',
            'valid_from' => 'date',
            'valid_until' => 'date',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
