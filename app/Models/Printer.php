<?php

namespace App\Models;

use App\Models\Traits\BelongsToOutlet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Printer extends Model
{
    use BelongsToOutlet;

    protected $fillable = [
        'name',
        'connection_type',
        'mac_address',
        'ip_address',
        'paper_width',
        'default',
        'outlet_id',
    ];

    protected function casts(): array
    {
        return [
            'paper_width' => 'integer',
            'default' => 'boolean',
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }
}
