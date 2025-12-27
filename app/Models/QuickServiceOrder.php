<?php

namespace App\Models;

use App\Models\Traits\BelongsToOutlet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuickServiceOrder extends Model
{
    use BelongsToOutlet, SoftDeletes;

    protected $fillable = [
        'order_number',
        'outlet_id',
        'cashier_id',
        'customer_id',
        'type',
        'status',
        'pax',
        'table_number',
        'customer_name',
        'notes',
        'subtotal',
        'tax',
        'tax_percentage',
        'service_charge',
        'service_charge_percentage',
        'discount',
        'discount_percentage',
        'total',
        'invoice_number',
        'payment_method',
        'payment_status',
        'paid_amount',
        'change',
        'saved_at',
        'cancelled_at',
        'cancel_reason',
    ];

    protected function casts(): array
    {
        return [
            'pax' => 'integer',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'tax_percentage' => 'decimal:2',
            'service_charge' => 'decimal:2',
            'service_charge_percentage' => 'decimal:2',
            'discount' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'change' => 'decimal:2',
            'saved_at' => 'datetime',
            'cancelled_at' => 'datetime',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuickServiceOrderItem::class);
    }

    public function recalculateTotals(): void
    {
        $this->subtotal = $this->items->sum('subtotal');

        $this->tax = ($this->subtotal * $this->tax_percentage) / 100;
        $this->service_charge = ($this->subtotal * $this->service_charge_percentage) / 100;

        $subtotalWithCharges = $this->subtotal + $this->tax + $this->service_charge;

        if ($this->discount_percentage > 0) {
            $this->discount = ($subtotalWithCharges * $this->discount_percentage) / 100;
        }

        $this->total = $subtotalWithCharges - $this->discount;
    }

    public function generateOrderNumber(): string
    {
        $prefix = 'QS';
        $year = date('Y');
        $latestOrder = self::whereYear('created_at', $year)
            ->where('outlet_id', $this->outlet_id)
            ->orderBy('id', 'desc')
            ->first();

        $number = $latestOrder ? intval(substr($latestOrder->order_number, -3)) + 1 : 1;

        return sprintf('%s-%03d-%s', $prefix, $number, $year);
    }

    public function generateInvoiceNumber(): string
    {
        $outlet = $this->outlet;
        $business = $outlet->business;

        $code = strtoupper(substr($business->name, 0, 4));
        $outletCode = str_pad($this->outlet_id, 2, '0', STR_PAD_LEFT);
        $date = date('Ymd');

        $latestOrder = self::whereNotNull('invoice_number')
            ->whereDate('created_at', today())
            ->where('outlet_id', $this->outlet_id)
            ->orderBy('id', 'desc')
            ->first();

        $number = $latestOrder ? intval(substr($latestOrder->invoice_number, -3)) + 1 : 1;

        return sprintf('%s%s%s%03d', $code, $outletCode, $date, $number);
    }
}
