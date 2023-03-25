<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class RoleFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'role_helper';
    }
}
