<?php

namespace App\Facades;

use App\Services\Config as ConfigService;
use Illuminate\Support\Facades\Facade;

class Config extends Facade
{
    public static function getFacadeAccessor()
    {
        return ConfigService::class;
    }
}
