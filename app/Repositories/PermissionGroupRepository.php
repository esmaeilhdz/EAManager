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

class PermissionGroupRepository implements Interfaces\iPermissionGroup
{
    use Common;

    public function getPermissionGroups($relation = [])
    {
        try {
            $permission_groups = PermissionGroup::select([
                'id',
                'name',
                'caption'
            ]);

            if (count($relation)) {
                $permission_groups->with($relation);
            }

            return $permission_groups->get();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
