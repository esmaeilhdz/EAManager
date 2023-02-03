<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PeriodSaleFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'period_sale_helper';
    }
}
