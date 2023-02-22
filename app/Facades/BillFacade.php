<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class BillFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'bill_helper';
    }
}
