<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class InvoiceFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'invoice_helper';
    }
}
