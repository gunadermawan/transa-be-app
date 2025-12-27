<?php

namespace App\Models;

use App\Models\Traits\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionInvoice extends Model
{
    use BelongsToBusiness;

    protected $fillable = [
        'business_id',
        'business_subscription_id',
        'invoice_number',
        'subtotal',
        'tax',
        'discount',
        'total',
        'issue_date',
        'due_date',
        'paid_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'issue_date' => 'date',
            'due_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function businessSubscription(): BelongsTo
    {
        return $this->belongsTo(BusinessSubscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }
}
