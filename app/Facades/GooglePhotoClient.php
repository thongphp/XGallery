<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class GooglePhotoClient extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'googlephoto';
    }
}
