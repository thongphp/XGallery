<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class GooglePhoto extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'googlephoto';
    }
}
