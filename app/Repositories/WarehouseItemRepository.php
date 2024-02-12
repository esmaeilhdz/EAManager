<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Accessory;
use App\Models\Cloth;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WarehouseItemRepository implements Interfaces\iWarehouseItem
{
    use Common;

    /**
     * لیست انبارها
     * @param $inputs
     * @param $user
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getWarehouseItems($inputs, $user): LengthAwarePaginator
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
     * @param $id
     * @param $user
     * @param array $select
     * @param array $relation
     * @return mixed
     * @throws ApiException
     */
    public function getWarehouseItemById($id, $user, $select = [], $relation = []): mixed
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Warehouse::where('id', $id)
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

    public function getWarehouseItemByData($inputs, $user, $select = [], $relation = [])
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Warehouse::where('warehouse_id', $inputs['warehouse_id'])
                ->where('model_type', $inputs['model_type'])
                ->where('model_id', $inputs['model_id'])
                ->where('place_id', $inputs['place_id'])
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

    /**
     * ویرایش انبار
     * @param $warehouse_item
     * @param $inputs
     * @param $user
     * @return mixed
     * @throws ApiException
     */
    public function editWarehouseItem($warehouse_item, $inputs, $user): mixed
    {
        try {
            switch ($inputs['model_type']) {
                case Cloth::class:
                    if ($inputs['sign'] == 'plus') {
                        $warehouse_item->merte += $inputs['merte'];
                    } elseif ($inputs['sign'] == 'minus') {
                        $warehouse_item->merte -= $inputs['merte'];
                    } else {
                        $warehouse_item->merte = $inputs['merte'];
                    }
                    break;
                case Accessory::class:
                    if ($inputs['sign'] == 'plus') {
                        $warehouse_item->count += $inputs['count'];
                    } elseif ($inputs['sign'] == 'minus') {
                        $warehouse_item->count -= $inputs['count'];
                    } else {
                        $warehouse_item->count = $inputs['count'];
                    }
                    break;
                case Product::class:
                    if ($inputs['sign'] == 'plus') {
                        $warehouse_item->pack_count += $inputs['pack_count'];
                        $warehouse_item->metre += $inputs['metre'];
                        $warehouse_item->count += $inputs['count'];
                    } elseif ($inputs['sign'] == 'minus') {
                        $warehouse_item->pack_count -= $inputs['pack_count'];
                        $warehouse_item->metre -= $inputs['metre'];
                        $warehouse_item->count -= $inputs['count'];
                    } else {
                        $warehouse_item->pack_count = $inputs['pack_count'];
                        $warehouse_item->metre = $inputs['metre'];
                        $warehouse_item->count = $inputs['count'];
                    }
                    break;
            }
            $warehouse_item->place_id = $inputs['place_id'];
            $warehouse_item->model_type = $inputs['model_type'];
            $warehouse_item->model_id = $inputs['model_id'];
            $warehouse_item->color_id = $inputs['color_id'];

            return $user->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن انبار
     * @param $inputs
     * @return array
     * @throws ApiException
     */
    public function addWarehouseItem($inputs): array
    {
        try {
            $warehouse_item = new WarehouseItem();

            $warehouse_item->warehouse_id = $inputs['warehouse_id'];
            $warehouse_item->place_id = $inputs['place_id'];
            $warehouse_item->model_type = $inputs['model_type'];
            $warehouse_item->model_id = $inputs['model_id'];
            $warehouse_item->free_size_count = $inputs['free_size_count'];
            $warehouse_item->size1_count = $inputs['size1_count'];
            $warehouse_item->size2_count = $inputs['size2_count'];
            $warehouse_item->size3_count = $inputs['size3_count'];
            $warehouse_item->size4_count = $inputs['size4_count'];
            $warehouse_item->color_id = $inputs['color_id'];
            $warehouse_item->merte = $inputs['merte'];
            $warehouse_item->count = $inputs['count'];

            $result = $warehouse_item->save();

            return [
                'result' => $result,
                'data' => $result ? $warehouse_item : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف انبار
     * @param $warehouse_item
     * @return mixed
     * @throws ApiException
     */
    public function deleteWarehouseItem($warehouse_item): mixed
    {
        try {
            return $warehouse_item->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
