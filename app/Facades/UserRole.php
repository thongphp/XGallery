<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class UserRole extends Facade
{
    public static function getFacadeAccessor()
    {
        return \App\Services\UserRole::class;
    }
}
