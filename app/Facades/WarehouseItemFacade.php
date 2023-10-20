<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class WarehouseItemFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'warehouse_item_helper';
    }
}
