<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PersonCompanyFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'person_company_helper';
    }
}
