<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ProvinceFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'province_helper';
    }
}
