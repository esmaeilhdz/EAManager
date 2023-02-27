<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class SalaryFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'salary_helper';
    }
}
