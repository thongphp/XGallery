<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class GoogleDriveFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'googledrive';
    }
}
