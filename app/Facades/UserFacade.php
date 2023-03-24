<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class UserFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'user_helper';
    }
}
