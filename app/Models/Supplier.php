<?php

namespace App\Models;

use App\Models\Traits\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use BelongsToBusiness, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'code',
        'contact_person',
        'phone',
        'email',
        'address',
        'payment_terms',
        'notes',
        'status',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
