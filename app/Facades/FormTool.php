<?php

namespace App\Facades;

use App\Services\FormTool as FormToolService;
use Illuminate\Support\Facades\Facade;

class FormTool extends Facade
{
    public static function getFacadeAccessor()
    {
        return FormToolService::class;
    }
}
