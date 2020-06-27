<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FlickrValidate extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'flickrvalidate';
    }
}
