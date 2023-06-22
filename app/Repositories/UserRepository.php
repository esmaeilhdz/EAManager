<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\User;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserRepository implements Interfaces\iUser
{
    use Common;

    /**
     * لیست افراد
     * @param $inputs
     * @param $user
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getUsers($inputs, $user): LengthAwarePaginator
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return User::query()
                ->with([
                    'creator:id,person_id',
                    'creator.person:id,name,family',
                    'person:id,name,family,national_code'
                ])
                ->select([
                    'code',
                    'person_id',
                    'mobile',
                    'email',
                    'created_by',
                    'created_at'
                ])
                ->whereHas('person', function ($q) use ($company_id, $inputs) {
                    $q->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                    ->whereHas('person_company', function ($q2) use ($company_id) {
                        $q2->where('company_id', $company_id);
                    });
                })
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات کاربر
     * @param $code
     * @param array $select
     * @return mixed
     * @throws ApiException
     */
    public function getUserByCode($code, $select = []): mixed
    {
        try {
            $user = User::with([
                'person:id,code,name,family,national_code,score',
            ]);

            if (count($select)) {
                $user = $user->select($select);
            }

            return $user->whereCode($code)->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش کاربر
     * @param $inputs
     * @param $user
     * @return mixed
     * @throws ApiException
     */
    public function editUser($inputs, $user): mixed
    {
        try {
            $user->email = $inputs['email'];
            $user->mobile = $inputs['mobile'];

            return $user->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن کاربر
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addUser($inputs, $user_login): array
    {
        try {
            $user = new User();

            $user->code = $this->randomString();
            $user->person_id = $inputs['person_id'];
            $user->email = $inputs['email'] ?? null;
            $user->mobile = $inputs['mobile'];
            $user->password = Hash::make($inputs['password']);
            $user->created_by = $user_login->id;

            $result = $user->save();

            return [
                'result' => $result,
                'data' => $result ? $user->code : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف کاربر
     * @param $user
     * @return mixed
     * @throws ApiException
     */
    public function deleteUser($user): mixed
    {
        try {
            return $user->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
