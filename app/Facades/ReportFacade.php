<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ReportFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'report_helper';
    }
}
