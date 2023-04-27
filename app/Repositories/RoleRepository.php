<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\RoleModel;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
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

    /**
     * دیتای نقش ها برای نمایش درختی
     * @param $inputs
     * @param $user
     * @return Collection|array
     */
    public function getRoleTree($inputs, $user): Collection|array
    {
        $roles = RoleModel::with([
            'children' => function ($q) {
                $q->select(['parent_id', 'code', 'name', 'caption'])->withoutGlobalScope('accessRole');
            }
        ])
            ->select([
                'id',
                'parent_id',
                'code',
                'name',
                'caption'
            ]);

        if (isset($inputs['parent_role_code'])) {
            $roles = $roles->where('code', $inputs['parent_role_code']);
        } else {
            $user_role = $user->roles[0];
            $roles = $roles->where('code', $user_role->code);
        }

        return $roles->withoutGlobalScope('accessRole')->get();
    }

    public function getRoleByCode($code, $select = [], $relation = [])
    {
        try {
            $role = RoleModel::whereCode($code);

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
            $role->caption = $inputs['caption'];

            return $role->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addRole($inputs, $user)
    {
        try {
            $role = new RoleModel();

            $role->code = $this->randomString();
            $role->name = $this->FaToEn($inputs['caption']);
            $role->caption = $inputs['caption'];
            $role->guard_name = $inputs['guard_name'];
            $role->created_by = $user->id;

            $result = $role->save();

            return [
                'result' => $result,
                'data' => $result ? $role->code : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
