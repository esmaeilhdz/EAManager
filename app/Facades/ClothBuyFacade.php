<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ClothBuyFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'cloth_buy_helper';
    }
}
