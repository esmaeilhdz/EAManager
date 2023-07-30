<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iPerson;
use App\Repositories\Interfaces\iRole;
use App\Repositories\Interfaces\iUser;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserHelper
{
    use Common;

    // attributes
    public iUser $user_interface;
    public iRole $role_interface;
    public iPerson $person_interface;

    public function __construct(
        iUser $user_interface,
        iRole $role_interface,
        iPerson $person_interface
    )
    {
        $this->user_interface = $user_interface;
        $this->role_interface = $role_interface;
        $this->person_interface = $person_interface;
    }

    /**
     * سرویس لیست افراد
     * @param $inputs
     * @return array
     */
    public function getUsers($inputs): array
    {
        $user = Auth::user();
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name;family;national_code;concat_ws(" ",name,family);replace(concat_ws("",name,family)," ","")');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'users');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $users = $this->user_interface->getUsers($inputs, $user);

        $users->transform(function ($item) {
            return [
                'code' => $item->code,
                'name' => $item->person->name,
                'family' => $item->person->family,
                'national_code' => $item->person->national_code,
                'email' => $item->email,
                'mobile' => $item->mobile,
                'creator' => is_null($item->creator->person) ? null : [
                    'user' => [
                        'full_name' => $item->creator->person->name . ' ' . $item->creator->person->family,
                    ]
                ],
                'created_at' => $item->created_at,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $users
        ];
    }

    /**
     * سرویس جزئیات کاربر
     * @param $code
     * @return array
     */
    public function getUserDetail($code): array
    {
        $user = $this->user_interface->getUserByCode($code);
        if (is_null($user)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $user
        ];
    }

    public function getUserInfo()
    {
        $user = Auth::user();

        $select = ['id', 'code', 'name', 'family', 'score'];
        $relation = [
            'attachment' => function ($q) {
                $q->select(['model_type', 'model_id', 'path', 'file_name', 'ext'])
                    ->where('attachment_type_id', 1)
                    ->where('type', 'avatar');
            }
        ];
        $person = $this->person_interface->getPersonById($user->person_id, $select, $relation);

        if (count($person->attachment)) {
            $avatar_address = env('APP_URL') . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . $person->attachment[0]->path . DIRECTORY_SEPARATOR . $person->attachment[0]->file_name . '.' . $person->attachment[0]->ext;
        } else {
            $avatar_address = env('APP_URL') . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'person_no_pic.png';
        }

        $result = [
            'code' => $person->code,
            'name' => $person->name,
            'family' => $person->family,
            'score' => $person->score,
            'avatar' => $avatar_address
        ];

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $result
        ];

    }

    public function editUserRole($inputs)
    {
        $user = $this->user_interface->getUserByCode($inputs['code']);
        if (is_null($user)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $role_names = [];
        foreach ($inputs['role_code'] as $role_code) {
            $role = $this->role_interface->getRoleByCode($role_code, ['id', 'name']);
            if (is_null($role)) {
                return [
                    'result' => false,
                    'message' => __('messages.role_not_found'),
                    'data' => null
                ];
            }
            $role_names[] = $role->name;
        }

        $result = $user->syncRoles($role_names);
        return [
            'result' => $result,
            'message' => $result ? __('messages.success') : __('messages.failed'),
            'data' => null
        ];
    }

    /**
     * سرویس ویرایش کاربر
     * @param $inputs
     * @return array
     */
    public function editUser($inputs): array
    {
        $user = $this->user_interface->getUserByCode($inputs['code']);
        if (is_null($user)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->user_interface->editUser($inputs, $user);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * سرویس افزودن کاربر
     * @param $inputs
     * @return array
     */
    public function addUser($inputs): array
    {
        $person = $this->person_interface->getPersonByCode($inputs['person_code'], ['id']);
        if (is_null($person)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['person_id'] = $person->id;
        $user = Auth::user();

        DB::beginTransaction();
        $res = $this->user_interface->addUser($inputs, $user);
        $result[] = $res['result'];
        $result[] = $this->role_interface->setRole($res['data'], 'default');

        if (!in_array(false, $result)) {
            $flag = true;
            $data = $res['data']->code;
            DB::commit();
        } else {
            $flag = false;
            $data = null;
            DB::rollBack();
        }

        return [
            'result' => $flag,
            'message' => $flag ? __('messages.success') : __('messages.fail'),
            'data' => $data
        ];
    }

    /**
     * سرویس حذف کاربر
     * @param $inputs
     * @return array
     */
    public function deleteUser($inputs): array
    {
        $user = $this->user_interface->getUserByCode($inputs['code'], ['id']);
        if (is_null($user)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->user_interface->deleteUser($user);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }


}
