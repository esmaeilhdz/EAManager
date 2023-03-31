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

        $permissions->transform(function ($item) {
            if ($item->permission_name == "admin-$item->name") {
                $admin_permission_id = $item->permission_id;
                $admin_selected = true;
                $edit_permission_id = $item->permission_id + 1;
                $edit_selected = false;
                $view_permission_id = $item->permission_id + 2;
                $view_selected = false;
            } elseif ($item->permission_name == "edit-$item->name") {
                $admin_permission_id = $item->permission_id - 1;
                $admin_selected = false;
                $edit_permission_id = $item->permission_id;
                $edit_selected = true;
                $view_permission_id = $item->permission_id + 1;
                $view_selected = false;
            } else {
                $admin_permission_id = $item->permission_id - 2;
                $admin_selected = false;
                $edit_permission_id = $item->permission_id - 1;
                $edit_selected = false;
                $view_permission_id = $item->permission_id;
                $view_selected = true;
            }

            return [
                'permission_group' => [
                    'caption' => $item->caption,
                    'name' => $item->name,
                    'permissions' => [
                        'admin' => [
                            'id' => $admin_permission_id,
                            'selected' => $admin_selected
                        ],
                        'edit' => [
                            'id' => $edit_permission_id,
                            'selected' => $edit_selected
                        ],
                        'view' => [
                            'id' => $view_permission_id,
                            'selected' => $view_selected
                        ]
                    ]
                ]
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $permissions
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
