<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class GooglePhotoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'googlephoto';
    }
}
