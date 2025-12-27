<?php

namespace App\Models;

use App\Models\Traits\BelongsToOutlet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    use BelongsToOutlet;

    protected $fillable = [
        'outlet_id',
        'name',
        'start_time',
        'end_time',
        'grace_period_minutes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime:H:i:s',
            'end_time' => 'datetime:H:i:s',
            'grace_period_minutes' => 'integer',
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
