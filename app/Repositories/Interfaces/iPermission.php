<?php

namespace App\Repositories\Interfaces;

interface iPermission
{
    public function getPermissionById($permission_id);

    public function getPermissionByName($permission_name);

    public function getRolePermissions($role_id);

    public function addRolePermission($role_id, $permission_id);

    public function deleteRolePermission($role_id, $permission_id);

    public function deleteRolePermissions($role_id, $permission_ids);
}
