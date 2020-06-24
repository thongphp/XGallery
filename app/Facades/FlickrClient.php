<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FlickrClient extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'flickr';
    }
}
