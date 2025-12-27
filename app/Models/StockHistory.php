<?php

namespace App\Models;

use App\Models\Traits\BelongsToOutlet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockHistory extends Model
{
    use BelongsToOutlet;

    protected $fillable = [
        'stock_id',
        'user_id',
        'outlet_id',
        'quantity',
        'current_stock',
        'type',
        'reference',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'current_stock' => 'integer',
        ];
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }
}
