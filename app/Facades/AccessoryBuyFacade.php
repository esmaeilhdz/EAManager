<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class AccessoryBuyFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'accessory_buy_helper';
    }
}
