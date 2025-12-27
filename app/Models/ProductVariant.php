<?php

namespace App\Models;

use App\Models\Traits\BelongsToProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use BelongsToProduct, SoftDeletes;

    protected $fillable = [
        'product_id',
        'variant_name',
        'sku',
        'barcode',
        'price_adjustment',
        'cost_adjustment',
        'attributes',
        'image',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price_adjustment' => 'decimal:2',
            'cost_adjustment' => 'decimal:2',
            'attributes' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
