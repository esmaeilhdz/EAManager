<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class GroupConversationFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'group_conversation_helper';
    }
}
