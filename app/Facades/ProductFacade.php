<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ProductFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'product_helper';
    }
}
