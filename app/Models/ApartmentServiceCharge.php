<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApartmentServiceCharge extends Model
{
    protected $fillable = [
        'apartment_id',
        'service_charge_id',
        'amount',
    ];
}
