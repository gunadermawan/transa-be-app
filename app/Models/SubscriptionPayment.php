<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriptionInvoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    use BelongsToSubscriptionInvoice;

    protected $fillable = [
        'subscription_invoice_id',
        'business_id',
        'payment_method',
        'amount',
        'transaction_id',
        'gateway',
        'payment_date',
        'status',
        'gateway_response',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'datetime',
        ];
    }

    public function subscriptionInvoice(): BelongsTo
    {
        return $this->belongsTo(SubscriptionInvoice::class);
    }
}
