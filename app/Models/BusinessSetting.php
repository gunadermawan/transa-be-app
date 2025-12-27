<?php

namespace App\Models;

use App\Models\Traits\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    use BelongsToBusiness;

    protected $fillable = [
        'business_id',
        'name',
        'charge_type',
        'type',
        'value',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
