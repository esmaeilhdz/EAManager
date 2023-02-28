<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ChatGroupFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'chat_group_helper';
    }
}
