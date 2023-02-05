<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ProductWarehouseFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'product_warehouse_helper';
    }
}
