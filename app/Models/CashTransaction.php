<?php

namespace App\Models;

use App\Models\Traits\BelongsToCashDrawerSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashTransaction extends Model
{
    use BelongsToCashDrawerSession;

    protected $fillable = [
        'cash_drawer_session_id',
        'type',
        'amount',
        'reference',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function cashDrawerSession(): BelongsTo
    {
        return $this->belongsTo(CashDrawerSession::class);
    }
}
