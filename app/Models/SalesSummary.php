<?php

namespace App\Models;

use App\Models\Traits\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesSummary extends Model
{
    use BelongsToBusiness;

    protected $fillable = [
        'business_id',
        'date',
        'total_sales',
        'total_tax',
        'total_discount',
        'total_profit',
        'total_quantity',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'total_sales' => 'integer',
            'total_tax' => 'integer',
            'total_discount' => 'integer',
            'total_profit' => 'integer',
            'total_quantity' => 'integer',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
