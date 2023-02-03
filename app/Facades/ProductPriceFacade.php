<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ProductPriceFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'product_price_helper';
    }
}
