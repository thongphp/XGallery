<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Flickr extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'flickr';
    }
}
