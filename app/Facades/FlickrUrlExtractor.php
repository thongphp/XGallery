<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FlickrUrlExtractor extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'flickr\urlextractor';
    }
}
