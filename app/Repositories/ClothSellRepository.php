<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\ClothSell;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClothSellRepository implements Interfaces\iClothSell
{
    use Common;

    /**
     * لیست فروش پارچه
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getClothSells($inputs): LengthAwarePaginator
    {
        try {
            return ClothSell::with([
                'cloth:id,name',
                'customer:id,name',
                'warehouse_place:id,name'
            ])
                ->select([
                    'id',
                    'cloth_id',
                    'customer_id',
                    'warehouse_place_id',
                    'metre',
                    'roll_count',
                    'sell_date',
                    'created_by',
                    'created_at'
                ])
                ->where('cloth_id', $inputs['cloth_id'])
                ->when(isset($inputs['search_txt']), function ($q) use ($inputs) {
                    $q->whereHas('customer', function ($q2) use ($inputs) {
                        $q2->where('name', 'like', '%' . $inputs['search_txt'] . '%');
                    })
                        ->orWhereHas('warehouse_place', function ($q3) use ($inputs) {
                            $q3->where('name', 'like', '%' . $inputs['search_txt'] . '%');
                    });
                })
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات فروش پارچه
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function getClothSellById($inputs): mixed
    {
        try {
            return ClothSell::with([
                'cloth:id,code,name',
                'customer:id,name',
                'warehouse_place:id,name'
            ])
                ->select([
                    'id',
                    'cloth_id',
                    'customer_id',
                    'warehouse_place_id',
                    'metre',
                    'roll_count',
                    'sell_date'
                ])
                ->where('cloth_id', $inputs['cloth_id'])
                ->where('id', $inputs['id'])
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editClothSell($cloth_sell, $inputs)
    {
        try {
            $cloth_sell->customer_id = $inputs['customer_id'];
            $cloth_sell->warehouse_place_id = $inputs['warehouse_place_id'];
            $cloth_sell->metre = $inputs['metre'];
            $cloth_sell->roll_count = $inputs['roll_count'];
            $cloth_sell->sell_date = $inputs['sell_date'];
            $cloth_sell->factor_no = $inputs['factor_no'];
            $cloth_sell->price = $inputs['price'];

            return $cloth_sell->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addClothSell($inputs, $user)
    {
        try {
            $cloth_sell = new ClothSell();

            $cloth_sell->cloth_id = $inputs['cloth_id'];
            $cloth_sell->customer_id = $inputs['customer_id'];
            $cloth_sell->warehouse_place_id = $inputs['warehouse_place_id'];
            $cloth_sell->metre = $inputs['metre'];
            $cloth_sell->roll_count = $inputs['roll_count'];
            $cloth_sell->sell_date = $inputs['sell_date'];
            $cloth_sell->factor_no = $inputs['factor_no'];
            $cloth_sell->price = $inputs['price'];
            $cloth_sell->created_by = $user->id;

            $result = $cloth_sell->save();

            return [
                'result' => $result,
                'data' => $result ? $cloth_sell->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteClothSell($cloth_sell)
    {
        return $cloth_sell->delete();
    }
}
