<?php

namespace App\Models;

use App\Models\Traits\BelongsToOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDiscount extends Model
{
    use BelongsToOrder;

    protected $fillable = [
        'order_id',
        'discount_id',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(BusinessSetting::class, 'discount_id');
    }
}
