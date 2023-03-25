<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\RoleModel;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

class RoleRepository implements Interfaces\iRole
{
    use Common;

    /**
     * لیست نقش ها
     * @param $inputs
     * @param $user
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getRoles($inputs, $user): LengthAwarePaginator
    {
        try {
            return RoleModel::with([
                'creator:id,person_id',
                'creator.person:id,name,family',
            ])
                ->select([
                    'code',
                    'caption',
                    'created_by',
                    'created_at'
                ])
                ->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getRoleByCode($code, $user, $select = [], $relation = [])
    {
        try {
            $role = Role::whereCode($code);

            if (count($select)) {
                $role = $role->select($select);
            }

            if (count($relation)) {
                $role = $role->with($relation);
            }

            return $role->first();

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editRole($role, $inputs)
    {
        try {
            $role->role_type_id = $inputs['role_type_id'];
            $role->role_id = $inputs['role_id'];
            $role->payment_id = $inputs['role_type_id'];

            return $role->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addRole($inputs, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $role = new Role();

            $role->company_id = $company_id;
            $role->role_type_id = $inputs['role_type_id'];
            $role->role_id = $inputs['role_id'];
            $role->payment_id = $inputs['payment_id'];
            $role->created_by = $user->id;

            $result = $role->save();

            return [
                'result' => $result,
                'data' => $result ? $role->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteRole($role)
    {
        try {
            return $role->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
