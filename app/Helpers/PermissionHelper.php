<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iPermission;
use App\Repositories\Interfaces\iRole;
use App\Traits\Common;

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

}
