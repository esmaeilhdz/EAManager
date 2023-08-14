<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\ClothBuy;
use App\Models\ClothWareHouse;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
                    'roll_count'
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

    public function editWarehouse($inputs)
    {
        try {
            $cloth_warehouse = ClothWareHouse::where('cloth_id', $inputs['cloth_id'])
                ->where('place_id', $inputs['warehouse_place_id'])
                ->first();

            if ($inputs['sign'] == 'plus') {
                $cloth_warehouse->metre += $inputs['metre'];
                $cloth_warehouse->roll_count += $inputs['roll_count'];
            } elseif ($inputs['sign'] == 'minus') {
                $cloth_warehouse->metre -= $inputs['metre'];
                $cloth_warehouse->roll_count -= $inputs['roll_count'];
            }

            return $cloth_warehouse->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
