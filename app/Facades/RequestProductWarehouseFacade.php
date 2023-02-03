<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class RequestProductWarehouseFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'request_product_warehouse_helper';
    }
}
