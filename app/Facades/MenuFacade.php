<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class MenuFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'menu_helper';
    }
}
