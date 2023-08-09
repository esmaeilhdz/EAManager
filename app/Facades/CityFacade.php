<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CityFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'city_helper';
    }
}
