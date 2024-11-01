<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Occupant extends Model
{
    protected $fillable = [
        'residence_id',
        'apartment_id',
        'name',
        'address',
        'occupation',
        'phone',
        'move_in_date',
        'move_out_date',
        'is_current_occupant',
    ];

    public function residence(): BelongsTo
    {
        return $this->belongsTo(Residence::class);
    }

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    public function revenues(): HasMany
    {
        return $this->hasMany(Revenue::class);
    }
}
