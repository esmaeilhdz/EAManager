<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\ClothBuy;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClothBuyRepository implements Interfaces\iClothBuy
{
    use Common;

    /**
     * لیست خرید پارچه
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getClothBuys($inputs): LengthAwarePaginator
    {
        try {
            return ClothBuy::with([
                'cloth:id,name',
                'seller_place:id,name',
                'warehouse_place:id,name'
            ])
                ->select([
                    'id',
                    'cloth_id',
                    'seller_place_id',
                    'warehouse_place_id',
                    'metre',
                    'roll_count',
                    'receive_date',
                    'created_by',
                    'created_at'
                ])
                ->where('cloth_id', $inputs['cloth_id'])
                ->where(function ($q) use ($inputs) {
                    $q->whereHas('seller_place', function ($q) use ($inputs) {
                        $q->whereRaw($inputs['where']['place']['condition'], $inputs['where']['place']['params']);
                    });
                    $q->orWhereHas('warehouse_place', function ($q) use ($inputs) {
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
     * جزئیات خرید پارچه
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function getClothBuyById($inputs): mixed
    {
        try {
            return ClothBuy::with([
                'cloth:id,code,name',
                'seller_place:id,name',
                'warehouse_place:id,name'
            ])
                ->select([
                    'id',
                    'cloth_id',
                    'seller_place_id',
                    'warehouse_place_id',
                    'metre',
                    'roll_count',
                    'receive_date'
                ])
                ->where('cloth_id', $inputs['cloth_id'])
                ->where('id', $inputs['id'])
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editClothBuy($cloth_buy, $inputs)
    {
        try {
            $cloth_buy->seller_place_id = $inputs['seller_place_id'];
            $cloth_buy->warehouse_place_id = $inputs['warehouse_place_id'];
            $cloth_buy->metre = $inputs['metre'];
            $cloth_buy->roll_count = $inputs['roll_count'];
            $cloth_buy->receive_date = $inputs['receive_date'];
            $cloth_buy->factor_no = $inputs['factor_no'];
            $cloth_buy->price = $inputs['price'];

            return $cloth_buy->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addClothBuy($inputs, $user)
    {
        try {
            $cloth_buy = new ClothBuy();

            $cloth_buy->cloth_id = $inputs['cloth_id'];
            $cloth_buy->seller_place_id = $inputs['seller_place_id'];
            $cloth_buy->warehouse_place_id = $inputs['warehouse_place_id'];
            $cloth_buy->metre = $inputs['metre'];
            $cloth_buy->roll_count = $inputs['roll_count'];
            $cloth_buy->receive_date = $inputs['receive_date'];
            $cloth_buy->factor_no = $inputs['factor_no'];
            $cloth_buy->price = $inputs['price'];
            $cloth_buy->created_by = $user->id;

            $result = $cloth_buy->save();

            return [
                'result' => $result,
                'data' => $result ? $cloth_buy->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteClothBuy($cloth_buy)
    {
        return $cloth_buy->delete();
    }
}
