<?php

namespace App\Models;

use App\Models\Traits\BelongsToOutlet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use BelongsToOutlet;

    protected $fillable = [
        'order_number',
        'outlet_id',
        'customer_id',
        'sub_total',
        'total_price',
        'total_items',
        'tax',
        'discount',
        'payment_method',
        'payment_status',
        'cash_received',
        'change',
        'notes',
        'status',
        'cashier_id',
    ];

    protected function casts(): array
    {
        return [
            'sub_total' => 'decimal:2',
            'total_price' => 'decimal:2',
            'total_items' => 'integer',
            'tax' => 'decimal:2',
            'discount' => 'decimal:2',
            'cash_received' => 'decimal:2',
            'change' => 'decimal:2',
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }
}
