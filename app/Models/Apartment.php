<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Apartment extends Model
{
    protected $fillable = [
        'residence_id',
        'apartment_number',
        'name',
        'description',
        'is_owner_apartment',
        'rent_amount',
        'is_available',
    ];

    public function residence(): BelongsTo
    {
        return $this->belongsTo(Residence::class);
    }

    public function occupants(): HasMany
    {
        return $this->hasMany(Occupant::class);
    }

    public function serviceCharges(): BelongsToMany
    {
        return $this->belongsToMany(ServiceCharge::class);
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
