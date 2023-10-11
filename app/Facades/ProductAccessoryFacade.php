<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ProductAccessoryFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'product_accessory_helper';
    }
}
