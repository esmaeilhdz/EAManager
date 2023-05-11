<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\PermissionGroup;
use App\Models\RoleModel;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionRepository implements Interfaces\iPermission
{
    use Common;

    public function getPermissionById($permission_id)
    {
        try {
            return Permission::select(['id', 'name'])->find($permission_id);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getPermissionByName($permission_name)
    {
        try {
            return Permission::select(['id', 'name'])->where('name', $permission_name)->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * پرمیشن های یک نقش
     * @param $role_id
     * @return Collection
     * @throws ApiException
     */
    public function getRolePermissions($role_id): Collection
    {
        try {
            return PermissionGroup::select([
                'permission_groups.id',
                'permission_groups.caption',
                'permission_groups.name',
                'rhp.permission_id',
                'rhp.role_id',
                'p.id as permission_id',
                'p.name as permission_name'
            ])
                ->join('permissions as p', 'permission_groups.id', '=', 'p.permission_group_id')
                ->leftjoin('role_has_permissions as rhp', 'p.id', '=', 'rhp.permission_id')
                ->where('rhp.role_id', $role_id)
                ->orWhereNull('rhp.role_id')
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addRolePermission($role_id, $permission_id): bool
    {
        try {
            return DB::table('role_has_permissions')->insert([
                [
                    'role_id' => $role_id,
                    'permission_id' => $permission_id
                ]
            ]);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteRolePermission($role_id, $permission_id): int
    {
        try {
            return DB::table('role_has_permissions')->where('role_id', $role_id)
                ->where('permission_id', $permission_id)
                ->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteRolePermissions($role_id, $permission_ids): int
    {
        try {
            return DB::table('role_has_permissions')->where('role_id', $role_id)
                ->whereIn('permission_id', $permission_ids)
                ->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
