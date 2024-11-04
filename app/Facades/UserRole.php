<?php

namespace App\Facades;

use App\Providers\UserRoleServiceProvider;
use Illuminate\Support\Facades\Facade;

class UserRole extends Facade
{
    protected static function getFacadeAccessor()
    {
        return UserRoleServiceProvider::class;
    }
}
