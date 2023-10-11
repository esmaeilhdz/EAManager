<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class AccessoryWarehouseFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'accessory_warehouse_helper';
    }
}
