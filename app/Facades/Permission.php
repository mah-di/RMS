<?php

namespace App\Facades;

use App\Providers\PermissionServiceProvider;
use Illuminate\Support\Facades\Facade;

class Permission extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PermissionServiceProvider::class;
    }
}
