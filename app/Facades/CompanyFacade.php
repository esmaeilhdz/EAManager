<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CompanyFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'company_helper';
    }
}
