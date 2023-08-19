<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ClothSellFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'cloth_sell_helper';
    }
}
