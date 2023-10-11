<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\ClothBuy;
use App\Models\ClothWareHouse;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
                'cloth:id,name',
                'place:id,name',
                'color:enum_id,enum_caption'
            ])
                ->select([
                    'cloth_id',
                    'place_id',
                    'color_id',
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
     * @param $color_id
     * @param $place_id
     * @return object|null
     * @throws ApiException
     */
    public function getClothWarehousesByCloth($cloth_id, $color_id, $place_id): object|null
    {
        try {
            return ClothWareHouse::with([
                'place:id,name'
            ])
                ->select([
                    'id',
                    'cloth_id',
                    'place_id',
                    'color_id',
                    'metre'
                ])
                ->where('cloth_id', $cloth_id)
                ->where('color_id', $color_id)
                ->where('place_id', $place_id)
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addWarehouse($inputs, $user)
    {
        try {
            $cloth_warehouse = new ClothWareHouse();

            $cloth_warehouse->cloth_id = $inputs['cloth_id'];
            $cloth_warehouse->place_id = $inputs['place_id'];
            $cloth_warehouse->metre = $inputs['metre'];
            $cloth_warehouse->color_id = $inputs['color_id'];
            $cloth_warehouse->created_by = $user->id;

            return $cloth_warehouse->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editWarehouse($inputs)
    {
        try {
            $cloth_warehouse = ClothWareHouse::where('cloth_id', $inputs['cloth_id'])
                ->where('place_id', $inputs['warehouse_place_id'])
                ->where('color_id', $inputs['color_id'])
                ->first();

            switch ($inputs['sign']) {
                case 'plus':
                    $cloth_warehouse->metre += $inputs['metre'];
                    break;
                case 'minus':
                    $cloth_warehouse->metre -= $inputs['metre'];
                    break;
//                default:
//                    $cloth_warehouse->metre = $inputs['metre'];
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
