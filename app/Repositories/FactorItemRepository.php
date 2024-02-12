<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Factor;
use App\Models\FactorItem;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FactorItemRepository implements Interfaces\iFactorItem
{
    use Common;

    public function getById($factor_id, $id, $select = [], $relation = [])
    {
        try {
            $factor_item = FactorItem::where('factor_id', $factor_id)
                ->where('id', $id);

            if ($select) {
                $factor_item = $factor_item->select($select);
            }

            if ($relation) {
                $factor_item = $factor_item->with($relation);
            }

            return $factor_item->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getByFactorId($factor_id, $inputs, $select = [], $relation = [])
    {
        try {
            $factor_item = FactorItem::where('factor_id', $factor_id);

            if (count($select)) {
                $factor_item = $factor_item->select($select);
            }

            if ($relation) {
                $factor_item = $factor_item->with($relation);
            }

            return $factor_item->orderByDesc('id')
                ->paginate($inputs['per_page'] ?? 10);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش محصول فاکتور
     * @param $factor_item
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editFactorItem($factor_item, $inputs): mixed
    {
        try {
            $factor_item->pack_count = $inputs['pack_count'] ?? null;
            $factor_item->metre = $inputs['metre'] ?? null;
            $factor_item->count = $inputs['count'] ?? null;
            $factor_item->price = $inputs['price'];

            return $factor_item->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن محصول فاکتور
     * @param array $inputs
     * @param $factor_id
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addFactorItem(array $inputs, $factor_id, $user): array
    {
        try {
            $factor_item = new FactorItem();

            $factor_item->factor_id = $factor_id;
            $factor_item->model_type = $inputs['model_type'];
            $factor_item->model_id = $inputs['model_id'];
            $factor_item->pack_count = $inputs['pack_count'] ?? null;
            $factor_item->metre = $inputs['metre'] ?? null;
            $factor_item->count = $inputs['count'] ?? null;
            $factor_item->price = $inputs['price'];

            $result = $factor_item->save();

            return [
                'result' => $result,
                'data' => $result ? $factor_item->id : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف محصولات فاکتور
     * @param $factor_id
     * @return mixed
     * @throws ApiException
     */
    public function deleteFactorItems($factor_id): mixed
    {
        try {
            return FactorItem::where('factor_id', $factor_id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف محصول فاکتور
     * @param $factor_id
     * @param $id
     * @return mixed
     * @throws ApiException
     */
    public function deleteFactorItem($factor_id, $id): mixed
    {
        try {
            return FactorItem::where('factor_id', $factor_id)->where('id', $id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
