<?php

namespace App\Models;

use App\Models\Traits\BelongsToOutlet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use BelongsToOutlet;

    protected $fillable = [
        'user_id',
        'outlet_id',
        'shift_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'clock_in' => 'datetime:H:i:s',
            'clock_out' => 'datetime:H:i:s',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
}
