<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class SewingFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'sewing_helper';
    }
}
