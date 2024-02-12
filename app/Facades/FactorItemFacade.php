<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FactorItemFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'factor_item_helper';
    }
}
