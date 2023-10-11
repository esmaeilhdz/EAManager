<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ProductModelFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'product_model_helper';
    }
}
