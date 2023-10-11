<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FactorPaymentFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'factor_payment_helper';
    }
}
