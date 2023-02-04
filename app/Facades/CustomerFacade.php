<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CustomerFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'customer_helper';
    }
}
