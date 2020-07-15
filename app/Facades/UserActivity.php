<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class UserActivity extends Facade
{
    public static function getFacadeAccessor()
    {
        return UserActivity::class;
    }
}
