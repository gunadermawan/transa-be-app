<?php

namespace App\Models;

use App\Models\Traits\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesTransaction extends Model
{
    use BelongsToBusiness;

    protected $fillable = [
        'date',
        'business_id',
        'outlet_id',
        'product_id',
        'quantity_sold',
        'total_sales',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'quantity_sold' => 'integer',
            'total_sales' => 'integer',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
