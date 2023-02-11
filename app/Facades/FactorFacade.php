<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FactorFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'factor_helper';
    }
}
