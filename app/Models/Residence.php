<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Residence extends Model
{
    protected $fillable = [
        'name',
        'location',
        'description',
    ];

    protected $casts = [
        'description' => 'array',
    ];

    public function apartments(): HasMany
    {
        return $this->hasMany(Apartment::class);
    }

    public function occupants(): HasMany
    {
        return $this->hasMany(Occupant::class);
    }

    public function revenues(): HasMany
    {
        return $this->hasMany(Revenue::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
