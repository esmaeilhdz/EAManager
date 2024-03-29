<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ChatFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'chat_helper';
    }
}
