<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class DesignModelFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'design_model_helper';
    }
}
