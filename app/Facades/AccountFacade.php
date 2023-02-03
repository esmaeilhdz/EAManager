<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class AccountFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'account_helper';
    }
}
