<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'role_id',
        'name',
        'slug',
        'type',
    ];

    public function role(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }
}
