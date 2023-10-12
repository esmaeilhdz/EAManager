<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class WarehouseFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'warehouse_helper';
    }
}
