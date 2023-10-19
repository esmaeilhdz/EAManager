<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Warehouse;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WarehouseRepository implements Interfaces\iWarehouse
{
    use Common;

    /**
     * لیست انبارها
     * @param $inputs
     * @param $user
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getWarehouses($inputs, $user): LengthAwarePaginator
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Warehouse::query()
                ->with([
                    'parent:id,name'
                ])
                ->select([
                    'id',
                    'parent_id',
                    'code',
                    'name',
                    'is_enable',
                    'created_at'
                ])
                ->where('company_id', $company_id)
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات انبار
     * @param $code
     * @param $user
     * @param array $select
     * @param array $relation
     * @return mixed
     * @throws ApiException
     */
    public function getWarehouseByCode($code, $user, $select = [], $relation = []): mixed
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Warehouse::whereCode($code)
                ->when(count($select), function ($q) use ($select) {
                    $q->select($select);
                })
                ->when(count($relation), function ($q) use ($relation) {
                    $q->with($relation);
                })
                ->where('company_id', $company_id)
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getWarehousesCombo($inputs, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Warehouse::select('id', 'name')
                ->when(isset($inputs['search_txt']), function ($q) use ($inputs) {
                    $q->where('name', 'like', '%' . $inputs['search_txt'] . '%');
                })
                ->where('company_id', $company_id)
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش انبار
     * @param $inputs
     * @param $user
     * @return mixed
     * @throws ApiException
     */
    public function editWarehouse($inputs, $user): mixed
    {
        try {
            $user->name = $inputs['name'];
            $user->is_enable = $inputs['is_enable'];

            return $user->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن انبار
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addWarehouse($inputs, $user): array
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $warehouse = new Warehouse();

            $warehouse->company_id = $company_id;
            $warehouse->parent_id = $inputs['parent_id'];
            $warehouse->code = $this->randomString();
            $warehouse->name = $inputs['name'];
            $warehouse->created_by = $user->id;

            $result = $warehouse->save();

            return [
                'result' => $result,
                'data' => $result ? $warehouse : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف انبار
     * @param $warehouse
     * @return mixed
     * @throws ApiException
     */
    public function deleteWarehouse($warehouse): mixed
    {
        try {
            return $warehouse->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
