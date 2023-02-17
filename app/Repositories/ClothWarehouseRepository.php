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
