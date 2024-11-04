<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PermissionServiceProvider::class, function ($app) {
            return new PermissionServiceProvider($app);
        });

        $this->app->singleton(UserRoleServiceProvider::class, function ($app) {
            return new UserRoleServiceProvider($app);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
