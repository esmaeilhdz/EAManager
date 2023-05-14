<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ClothWarehouseFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'cloth_warehouse_helper';
    }
}
