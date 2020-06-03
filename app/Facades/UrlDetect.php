<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class UrlDetect extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'urldetect';
    }
}
