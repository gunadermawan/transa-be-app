<?php

namespace App\Models;

use App\Models\Traits\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use BelongsToBusiness;

    protected $fillable = ['name', 'business_id'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
