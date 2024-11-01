<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCharge extends Model
{
    protected $fillable = [
        'name',
        'frequency',
        'charge',
    ];

    public function revenues(): HasMany
    {
        return $this->hasMany(Revenue::class);
    }
}
