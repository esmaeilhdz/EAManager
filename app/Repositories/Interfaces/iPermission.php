<?php

namespace App\Repositories\Interfaces;

interface iPermission
{
    public function getRolePermissions($role_id);

    public function editRolePermissions($inputs);
}
