<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class AttachmentFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'attachment_helper';
    }
}
