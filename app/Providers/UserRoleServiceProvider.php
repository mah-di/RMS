<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class UserRoleServiceProvider extends ServiceProvider
{
    protected $roles = [];

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role)
            if ($this->hasRole($role))
                return true;

        return false;
    }

    public function hasAllRole(array $roles): bool
    {
        foreach ($roles as $role)
            if (! $this->hasRole($role))
                return false;

        return true;
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
