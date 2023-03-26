<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\PermissionGroup;
use App\Models\RoleModel;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

class PermissionRepository implements Interfaces\iPermission
{
    use Common;

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
                'permission_groups.caption',
                'permission_groups.name',
                'rhp.permission_id',
                'p.id as permission_id',
                'p.name as permission_name'
            ])
                ->join('permissions as p', 'permission_groups.id', '=', 'p.permission_group_id')
                ->join('role_has_permissions as rhp', 'p.id', '=', 'rhp.permission_id')
                ->where('rhp.role_id', $role_id)
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editRolePermissions($inputs)
    {
        // TODO: Implement editRolePermissions() method.
    }
}
