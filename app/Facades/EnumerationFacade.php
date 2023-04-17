<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class EnumerationFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'enumeration_helper';
    }
}
