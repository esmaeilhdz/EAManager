<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class SalePeriodFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'sale_period_helper';
    }
}
