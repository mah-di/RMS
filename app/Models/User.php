<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'otp'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'otp'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function permissions()
    {
        return $this->roles()
            ->join('permissions', 'roles.id', '=', 'permissions.role_id')
            ->select('permissions.*');
    }

    public function hasPermissionTo($permission): bool
    {
        [$type, $slug] = explode(':', $permission);

        return $this->permissions()->where([
                'permissions.slug' => $slug,
                'permissions.type' => $type
            ])->count() > 0;
    }

    public function hasRole($role): bool
    {
        return $this->roles()->where('slug', $role)->count() > 0;
    }

    public function hasAnyRole($roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->count() > 0;
    }

    public function hasAllRoles($roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->count() === count($roles);
    }

    public function revenuesCreated(): HasMany
    {
        return $this->hasMany(Revenue::class, 'created_by');
    }

    public function expensesCreated(): HasMany
    {
        return $this->hasMany(Expense::class, 'created_by');
    }
}
