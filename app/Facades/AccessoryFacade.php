<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class AccessoryFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'accessory_helper';
    }
}
