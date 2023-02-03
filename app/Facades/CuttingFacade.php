<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CuttingFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'cutting_helper';
    }
}
