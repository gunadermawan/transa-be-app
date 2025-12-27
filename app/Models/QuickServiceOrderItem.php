<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuickServiceOrderItem extends Model
{
    protected $fillable = [
        'quick_service_order_id',
        'product_id',
        'product_name',
        'product_image',
        'quantity',
        'price',
        'subtotal',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(QuickServiceOrder::class, 'quick_service_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted(): void
    {
        static::saved(function ($item) {
            $item->order->recalculateTotals();
            $item->order->save();
        });

        static::deleted(function ($item) {
            $item->order->recalculateTotals();
            $item->order->save();
        });
    }
}
