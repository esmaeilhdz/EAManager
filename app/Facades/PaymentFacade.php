<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PaymentFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'payment_helper';
    }
}
