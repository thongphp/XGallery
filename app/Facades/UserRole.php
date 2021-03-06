<?php

namespace App\Facades;

use App\Services\UserRole as UserRoleService;
use Illuminate\Support\Facades\Facade;

class UserRole extends Facade
{
    public static function getFacadeAccessor()
    {
        return UserRoleService::class;
    }
}
