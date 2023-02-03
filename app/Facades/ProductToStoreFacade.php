<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ProductToStoreFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'product_to_store_helper';
    }
}
