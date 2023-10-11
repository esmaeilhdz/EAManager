<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FactorProductFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'factor_product_helper';
    }
}
