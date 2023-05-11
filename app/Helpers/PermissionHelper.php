<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iPermission;
use App\Repositories\Interfaces\iRole;
use App\Traits\Common;
use Illuminate\Support\Facades\DB;

class PermissionHelper
{
    use Common;

    // attributes
    public iRole $role_interface;
    public iPermission $permission_interface;

    public function __construct(
        iPermission $permission_interface,
        iRole $role_interface
    )
    {
        $this->role_interface = $role_interface;
        $this->permission_interface = $permission_interface;
    }

    /**
     * پرمیشن های یک نقش
     * @param $role_code
     * @return array
     */
    public function getRolePermissions($role_code): array
    {
        $role = $this->role_interface->getRoleByCode($role_code, ['id']);
        if (is_null($role)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $permissions = $this->permission_interface->getRolePermissions($role->id);

        $return = null;
        foreach ($permissions as $permission) {
            $return[$permission->id]['resource'] = [
                'caption' => $permission->caption,
                'name' => $permission->name,
            ];

            $permission_type = explode('-', $permission->permission_name)[0];

            $return[$permission->id]['permissions'][$permission_type] = [
                'id' => $permission->permission_id,
                'selected' => (bool) !is_null($permission->role_id)
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => array_values($return)
        ];

    }

    public function editRolePermissions($inputs): array
    {
        $role = $this->role_interface->getRoleByCode($inputs['code'], ['id']);
        if (is_null($role)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $permission = $this->permission_interface->getPermissionById($inputs['id']);
        if (is_null($permission)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        list($resource, $permission_type) = explode('-', $permission->name);

        DB::beginTransaction();
        // delete permission
        if ($inputs['status'] == 0) {
            $result[] = $this->permission_interface->deleteRolePermission($role->id, $permission->id);
        } elseif ($inputs['status'] == 1) {
            // edit permission
            if ($permission_type == 'admin') {
                $permission_ids = [
                    $permission->id,
                    $this->permission_interface->getPermissionByName("edit-$resource")->id,
                    $this->permission_interface->getPermissionByName("view-$resource")->id,
                ];
            } elseif ($permission_type == 'edit') {
                $permission_ids = [
                    $this->permission_interface->getPermissionByName("admin-$resource")->id,
                    $permission->id,
                    $this->permission_interface->getPermissionByName("view-$resource")->id,
                ];
            } else {
                $permission_ids = [
                    $this->permission_interface->getPermissionByName("admin-$resource")->id,
                    $this->permission_interface->getPermissionByName("edit-$resource")->id,
                    $permission->id,
                ];
            }
            $result[] = $this->permission_interface->deleteRolePermissions($role->id, $permission_ids);
            $result[] = $this->permission_interface->addRolePermission($role->id, $permission->id);
        } else {
            // add permission
            $result[] = $this->permission_interface->addRolePermission($role->id, $permission->id);
        }

        if (!in_array(false, $result)) {
            $flag = true;
            DB::commit();
        } else {
            $flag = false;
            DB::rollBack();
        }

        return [
            'result' => $flag,
            'message' => $flag ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
