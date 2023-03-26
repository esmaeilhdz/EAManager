<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PermissionFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'permission_helper';
    }
}
