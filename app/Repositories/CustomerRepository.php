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
                'parent:id,name',
                'address:model_type,model_id,province_id,city_id,address',
                'address.province:id,name',
                'address.city:id,name',
            ])
                ->select([
                    'id',
                    'code',
                    'parent_id',
                    'name',
                    'mobile',
                    'score',
                    'created_by',
                    'created_at'
                ])
                ->when(isset($inputs['search_txt']), function ($q) use ($inputs) {
                    $q->whereHas('address.province', function ($q2) use ($inputs) {
                        $q2->whereLike('name', $inputs['search_txt']);
                    });
                    $q->orWhereHas('address.city', function ($q2) use ($inputs) {
                        $q2->whereLike('name', $inputs['search_txt']);
                    });
                })
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات مشتری
     * @param $code
     * @param array $select
     * @param array $relation
     * @return mixed
     * @throws ApiException
     */
    public function getCustomerByCode($code, $select = [], $relation = []): mixed
    {
        try {
            $customer = Customer::whereCode($code);

            if (count($relation)) {
                $customer = $customer->with($relation);
            }

            if (count($select)) {
                $customer = $customer->select($select);
            }

            return $customer->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getCustomersCombo($inputs)
    {
        try {
            return Customer::select('code', 'name')
                ->when(isset($inputs['search_txt']), function ($q) use ($inputs) {
                    $q->whereLike('name', $inputs['search_txt']);
                })
                ->limit(10)
                ->get();
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
            $company_id = $this->getCurrentCompanyOfUser($user);
            $customer = new Customer();

            $customer->code = $this->randomString();
            $customer->parent_id = $inputs['parent_id'] ?? null;
            $customer->company_id = $company_id;
            $customer->name = $inputs['name'];
            $customer->mobile = $inputs['mobile'];
            $customer->created_by = $user->id;

            $result = $customer->save();

            return [
                'result' => $result,
                'data' => $result ? $customer : null
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
