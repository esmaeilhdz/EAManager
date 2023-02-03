<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PlaceFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'place_helper';
    }
}
