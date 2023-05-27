<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class SalaryDeductionFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'salary_deduction_helper';
    }
}
