<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ClothFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'cloth_helper';
    }
}
