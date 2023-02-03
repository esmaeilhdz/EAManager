<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PersonFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'person_helper';
    }
}
