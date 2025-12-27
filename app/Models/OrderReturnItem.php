<?php

namespace App\Models;

use App\Models\Traits\BelongsToOrderReturn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturnItem extends Model
{
    use BelongsToOrderReturn;

    protected $fillable = [
        'order_return_id',
        'order_item_id',
        'product_id',
        'quantity',
        'price',
        'total_refund',
        'condition',
        'restock',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price' => 'decimal:2',
            'total_refund' => 'decimal:2',
            'restock' => 'boolean',
        ];
    }

    public function orderReturn(): BelongsTo
    {
        return $this->belongsTo(OrderReturn::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
