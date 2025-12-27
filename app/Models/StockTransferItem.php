<?php

namespace App\Models;

use App\Models\Traits\BelongsToStockTransfer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransferItem extends Model
{
    use BelongsToStockTransfer;

    protected $fillable = [
        'stock_transfer_id',
        'product_id',
        'quantity_requested',
        'quantity_sent',
        'quantity_received',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity_requested' => 'integer',
            'quantity_sent' => 'integer',
            'quantity_received' => 'integer',
        ];
    }

    public function stockTransfer(): BelongsTo
    {
        return $this->belongsTo(StockTransfer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
