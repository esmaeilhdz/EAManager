<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Customer;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerRepository implements Interfaces\iCustomer
{
    use Common;

    /**
     * لیست مشتری ها
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getCustomers($inputs): LengthAwarePaginator
    {
        try {
            return Customer::with([
                'creator:id,person_id',
                'creator.person:id,name,family',
                'parent:id,name'
            ])
                ->select([
                    'code',
                    'parent_id',
                    'name',
                    'mobile',
                    'score',
                    'created_by',
                    'created_at'
                ])
                ->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات مشتری
     * @param $code
     * @return mixed
     * @throws ApiException
     */
    public function getCustomerByCode($code): mixed
    {
        try {
            return Customer::with([
                'parent:id,name',
                'address:model_type,model_id,province_id,city_id,address,tel',
                'address.province:id,name',
                'address.city:id,name',
            ])
                ->select([
                    'id',
                    'code',
                    'parent_id',
                    'name',
                    'mobile',
                    'score'
                ])
                ->whereCode($code)
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش مشتری
     * @param $customer
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editCustomer($customer, $inputs): mixed
    {
        try {
            if (isset($inputs['parent_id'])) {
                $customer->parent_id = $inputs['parent_id'];
            }
            $customer->name = $inputs['name'];
            $customer->mobile = $inputs['mobile'];
            $customer->score = $inputs['score'];

            return $customer->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن مشتری
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addCustomer($inputs, $user): array
    {
        try {
            $customer = new Customer();

            $customer->code = $this->randomString();
            $customer->parent_id = $inputs['parent_id'] ?? null;
            $customer->name = $inputs['name'];
            $customer->mobile = $inputs['mobile'];
            $customer->score = $inputs['score'];
            $customer->created_by = $user->id;

            $result = $customer->save();

            return [
                'result' => $result,
                'data' => $result ? $customer->code : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف مشتری
     * @param $customer
     * @return mixed
     * @throws ApiException
     */
    public function deleteCustomer($customer): mixed
    {
        try {
            return $customer->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
