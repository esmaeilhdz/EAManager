<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class NotifFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'notif_helper';
    }
}
