<?php

namespace App\Helpers;

use App\Facades\RoleFacade;
use App\Repositories\Interfaces\iPermission;
use App\Repositories\Interfaces\iPermissionGroup;
use App\Repositories\Interfaces\iRole;
use App\Traits\Common;
use Illuminate\Support\Facades\DB;

class PermissionHelper
{
    use Common;

    // attributes
    public iRole $role_interface;
    public iPermission $permission_interface;
    public iPermissionGroup $permission_group_interface;

    public function __construct(
        iPermissionGroup $permission_group_interface,
        iPermission $permission_interface,
        iRole $role_interface
    )
    {
        $this->role_interface = $role_interface;
        $this->permission_interface = $permission_interface;
        $this->permission_group_interface = $permission_group_interface;
    }

    /**
     * پرمیشن های یک نقش
     * @param $role_code
     * @return array
     */
    public function getRolePermissions($role_code): array
    {
        $role = $this->role_interface->getRoleByCode($role_code, ['id', 'parent_id']);
        if (is_null($role)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $relation = [
            'permissions:id,permission_group_id,name'
        ];
        $permission_groups = $this->permission_group_interface->getPermissionGroups($relation)->toArray();
        $permissions = $this->permission_interface->getRolePermissions($role->id)->toArray();
        $permission_names = array_column($permissions, 'name');
        $parent_role_permissions = $this->permission_interface->getRolePermissions($role->parent_id)->toArray();
        $parent_role_permission_names = array_column($parent_role_permissions, 'permission_name');

        $return = null;
        foreach ($permission_groups as $permission_group) {
            if (in_array($permission_group['name'], $permission_names)) {
                $return[$permission_group['id']] = [
                    'name' => $permission_group['name'],
                    'caption' => $permission_group['caption'],
                    'permissions' => []
                ];
                foreach ($permission_group['permissions'] as $permission) {
                    list($permission_type, $resource) = explode('-', $permission['name']);

                    $selected = false;
                    $key = array_search($permission['name'], array_column($permissions, 'permission_name'));
                    if (is_numeric($key)) {
                        $permission_target = $permissions[$key];
                        if ($permission['name'] == $permission_target['permission_name']) {
                            $selected = true;
                        }
                    }

                    $is_show = false;
                    if ($permission_type == 'admin') {
                        if (count($parent_role_permission_names)) {
                            if (in_array($permission['name'], $parent_role_permission_names)) {
                                $is_show = true;
                                $is_shows[$resource][$permission_type] = $is_show;
                            } else {
                                $is_shows[$resource][$permission_type] = false;
                            }
                        } else {
                            $is_show = true;
                            $is_shows[$resource][$permission_type] = true;
                        }
                    } elseif ($permission_type == 'edit') {
                        if (count($parent_role_permission_names)) {
                            if (
                                $is_shows[$resource]['admin'] ||
                                (in_array($permission['name'], $parent_role_permission_names))
                            ) {
                                $is_show = true;
                                $is_shows[$resource][$permission_type] = $is_show;
                            } else {
                                $is_shows[$resource][$permission_type] = false;
                            }
                        } else {
                            $is_show = true;
                            $is_shows[$resource][$permission_type] = true;
                        }
                    } elseif ($permission_type == 'view') {
                        if (count($parent_role_permission_names)) {
                            if (
                                $is_shows[$resource]['admin'] ||
                                $is_shows[$resource]['edit'] ||
                                in_array($permission['name'], $parent_role_permission_names)
                            ) {
                                $is_show = true;
                            }
                        } else {
                            $is_show = true;
                            $is_shows[$resource][$permission_type] = true;
                        }
                    }

                    $permission_item[$permission_type] = [
                        'id' => $permission['id'],
                        'selected' => $selected,
                        'is_show' => $is_show
                    ];
                    $return[$permission_group['id']]['permissions'] = $permission_item;
                }
            } else {
                $permission_item = null;
                $return[$permission_group['id']] = [
                    'name' => $permission_group['name'],
                    'caption' => $permission_group['caption'],
                    'permissions' => []
                ];
                foreach ($permission_group['permissions'] as $permission) {
                    list($permission_type, $resource) = explode('-', $permission['name']);

                    $is_show = false;
                    if ($permission_type == 'admin') {
                        if (in_array($permission['name'], $parent_role_permission_names)) {
                            $is_show = true;
                            $is_shows[$resource][$permission_type] = $is_show;
                        } else {
                            $is_shows[$resource][$permission_type] = false;
                        }
                    } elseif ($permission_type == 'edit') {
                        if (
                            $is_shows[$resource]['admin'] ||
                            (in_array($permission['name'], $parent_role_permission_names))
                        ) {
                            $is_show = true;
                            $is_shows[$resource][$permission_type] = $is_show;
                        } else {
                            $is_shows[$resource][$permission_type] = false;
                        }
                    } elseif ($permission_type == 'view') {
                        if (
                            $is_shows[$resource]['admin'] ||
                            $is_shows[$resource]['edit'] ||
                            in_array($permission['name'], $parent_role_permission_names)
                        ) {
                            $is_show = true;
                        }
                    }

                    $permission_type = explode('-', $permission['name'])[0];
                    $permission_item[$permission_type] = [
                        'id' => $permission['id'],
                        'selected' => false,
                        'is_show' => $is_show
                    ];
                    $return[$permission_group['id']]['permissions'] = $permission_item;
                }
            }
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => array_values($return)
        ];

    }

    public function editRolePermissions($inputs): array
    {
        $role = $this->role_interface->getRoleByCode($inputs['code'], ['id', 'parent_id']);
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

        list($permission_type, $resource) = explode('-', $permission->name);

        DB::beginTransaction();
        // delete permission
        if ($inputs['status'] == 0) {
            $result[] = $this->permission_interface->deleteRolePermission($role->id, $permission->id);
            // delete this permission from all children of this role
            $children_roles = RoleFacade::getAllChildrenRoles($role);
            foreach ($children_roles as $children_role_id) {
                $result[] = $this->permission_interface->deleteRolePermission($children_role_id, $permission->id);
            }
        } elseif ($inputs['status'] == 1) {
            // edit permission
            if ($permission_type == 'admin') {
                $permission_ids = [
                    $permission->id,
                    $this->permission_interface->getPermissionByName("edit-$resource")->id,
                    $this->permission_interface->getPermissionByName("view-$resource")->id,
                ];
            } elseif ($permission_type == 'edit') {
                $admin_id = $this->permission_interface->getPermissionByName("admin-$resource")->id;
                $view_id = $this->permission_interface->getPermissionByName("view-$resource")->id;
                $permission_ids = [
                    $admin_id,
                    $permission->id,
                    $view_id,
                ];

                $children_roles = RoleFacade::getAllChildrenRoles($role);
                foreach ($children_roles as $children_role_id) {
                    $result[] = $this->permission_interface->editRolePermission($children_role_id, $permission->id, $admin_id);
                }
            } else {
                $admin_id = $this->permission_interface->getPermissionByName("admin-$resource")->id;
                $edit_id = $this->permission_interface->getPermissionByName("edit-$resource")->id;
                $permission_ids = [
                    $this->permission_interface->getPermissionByName("admin-$resource")->id,
                    $this->permission_interface->getPermissionByName("edit-$resource")->id,
                    $permission->id,
                ];

                $children_roles = RoleFacade::getAllChildrenRoles($role);
                foreach ($children_roles as $children_role_id) {
                    $res = $this->permission_interface->editRolePermission($children_role_id, $permission->id, $admin_id);
                    if ($res > 0) {
                        $result[] = $res;
                    }
                    $res = $this->permission_interface->editRolePermission($children_role_id, $permission->id, $edit_id);
                    if ($res > 0) {
                        $result[] = $res;
                    }
                }
            }
            $res = $this->permission_interface->deleteRolePermissions($role->id, $permission_ids);
            if ($res > 0) {
                $result[] = $res;
            }
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
