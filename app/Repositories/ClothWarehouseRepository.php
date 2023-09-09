<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\ClothBuy;
use App\Models\ClothWareHouse;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ClothWarehouseRepository implements Interfaces\iClothWarehouse
{
    use Common;

    /**
     * انبارهای پارچه
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getClothWarehouses($inputs): LengthAwarePaginator
    {
        try {
            return ClothWareHouse::with([
                'place:id,name'
            ])
                ->select([
                    'cloth_id',
                    'place_id',
                    'metre',
                ])
                ->where('cloth_id', $inputs['cloth_id'])
                ->where(function ($q) use ($inputs) {
                    $q->whereHas('place', function ($q) use ($inputs) {
                        $q->whereRaw($inputs['where']['place']['condition'], $inputs['where']['place']['params']);
                    });
                })
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * @param $cloth_id
     * @param $place_id
     * @throws ApiException
     */
    public function getClothWarehousesByCloth($cloth_id, $place_id)
    {
        try {
            return ClothWareHouse::with([
                'place:id,name'
            ])
                ->select([
                    'cloth_id',
                    'place_id',
                    'metre',
                    'roll_count'
                ])
                ->where('cloth_id', $cloth_id)
                ->where('place_id', $place_id)
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editWarehouse($inputs)
    {
        try {
            $cloth_warehouse = ClothWareHouse::where('cloth_id', $inputs['cloth_id'])
                ->where('place_id', $inputs['warehouse_place_id'])
                ->first();

            if ($inputs['sign'] == 'plus') {
                $cloth_warehouse->metre += $inputs['metre'];
            } elseif ($inputs['sign'] == 'minus') {
                $cloth_warehouse->metre -= $inputs['metre'];
            }

            return $cloth_warehouse->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editWarehouseMetre($inputs)
    {
        try {
            $cloth_warehouse = ClothWareHouse::where('cloth_id', $inputs['cloth_id'])
                ->where('place_id', $inputs['warehouse_place_id'])
                ->first();

            if ($inputs['sign'] == 'plus') {
                $cloth_warehouse->metre += $inputs['metre'];
            } elseif ($inputs['sign'] == 'minus') {
                $cloth_warehouse->metre -= $inputs['metre'];
            }

            return $cloth_warehouse->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editWarehouseRollCount($inputs)
    {
        try {
            $cloth_warehouse = ClothWareHouse::where('cloth_id', $inputs['cloth_id'])
                ->where('place_id', $inputs['warehouse_place_id'])
                ->first();

            return $cloth_warehouse->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
