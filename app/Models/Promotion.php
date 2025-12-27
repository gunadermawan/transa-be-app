<?php

namespace App\Models;

use App\Models\Traits\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use BelongsToBusiness, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'min_purchase_amount',
        'applicable_to',
        'start_date',
        'end_date',
        'max_uses',
        'used_count',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_purchase_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'max_uses' => 'integer',
            'used_count' => 'integer',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
