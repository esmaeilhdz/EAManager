<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iRole;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoleHelper
{
    use Common;

    // attributes
    public iRole $role_interface;

    public function __construct(iRole $role_interface)
    {
        $this->role_interface = $role_interface;
    }

    /**
     * لیست قبض ها
     * @param $inputs
     * @return array
     */
    public function getRoles($inputs): array
    {
        $user = Auth::user();
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:caption');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'roles');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $roles = $this->role_interface->getRoles($inputs, $user);

        $roles->transform(function ($item) {
            return [
                'code' => $item->code,
                'caption' => $item->caption,
                'creator' => is_null($item->creator->person) ? null : [
                    'person' => [
                        'full_name' => $item->creator->person->name . ' ' . $item->creator->person->family,
                    ]
                ],
                'created_at' => $item->created_at,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $roles
        ];
    }

    /**
     * جزئیات قبض
     * @param $id
     * @return array
     */
    public function getRoleDetail($id): array
    {
        $user = Auth::user();
        $select = ['id', 'role_type_id', 'role_id', 'payment_id'];
        $relation = [
            'role_type:enum_id,enum_caption',
            'payment:model_type,model_id,account_id,payment_date,payment_tracking_code,description,price',
            'payment.account:id,branch_name'
        ];
        $role = $this->role_interface->getRoleById($id, $user, $select, $relation);
        if (is_null($role)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $role
        ];
    }

    /**
     * ویرایش قبض
     * @param $inputs
     * @return array
     */
    public function editRole($inputs): array
    {
        $user = Auth::user();
        $role = $this->role_interface->getRoleById($inputs['id'], $user);
        if (is_null($role)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->role_interface->editRole($role, $inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * افزودن قبض
     * @param $inputs
     * @return array
     */
    public function addRole($inputs): array
    {
        $user = Auth::user();
        $result = $this->role_interface->addRole($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function deleteRole($id): array
    {
        $user = Auth::user();
        $role = $this->role_interface->getRoleById($id, $user, ['id']);
        if (is_null($role)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['model_type'] = $this->convertModelNameToNamespace('role');
        $inputs['model_id'] = $id;
        DB::beginTransaction();
        $result[] = $this->role_interface->deleteRole($id);
        $result[] = $this->payment_interface->deletePaymentsResource($inputs, $user);

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
