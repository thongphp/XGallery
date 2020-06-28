<?php

namespace App\Facades\Flickr;

use Illuminate\Support\Facades\Facade;

class UrlExtractor extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'flickrurlextractor';
    }
}
