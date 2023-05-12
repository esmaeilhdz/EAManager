<?php

namespace App\Repositories\Interfaces;

interface iPermissionGroup
{
    public function getPermissionGroups($relation = []);
}
