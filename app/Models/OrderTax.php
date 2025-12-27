<?php

namespace App\Models;

use App\Models\Traits\BelongsToOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTax extends Model
{
    use BelongsToOrder;

    protected $fillable = [
        'order_id',
        'tax_id',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(BusinessSetting::class, 'tax_id');
    }
}
