<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\ClothBuy;
use App\Models\ClothBuyItem;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClothBuyItemsRepository implements Interfaces\iClothBuyItems
{
    use Common;

    /**
     * لیست خرید پارچه
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getClothBuyItems($inputs): LengthAwarePaginator
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
                    'receive_date',
                    'factor_no',
                    'price',
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
    public function getClothBuyItemById($inputs): mixed
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
                    'factor_no',
                    'price',
                    'receive_date'
                ])
                ->where('cloth_id', $inputs['cloth_id'])
                ->where('id', $inputs['id'])
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addClothBuyItem($inputs, $user): array
    {
        try {
            $cloth_buy_item = new ClothBuyItem();

            $cloth_buy_item->cloth_buy_id = $inputs['cloth_buy_id'];
            $cloth_buy_item->color_id = $inputs['color_id'];
            $cloth_buy_item->metre = $inputs['metre'];
            $cloth_buy_item->unit_price = $inputs['price'];
            $cloth_buy_item->price = $inputs['price'] * $inputs['metre'];

            $result = $cloth_buy_item->save();

            return [
                'result' => $result,
                'data' => $result ? $cloth_buy_item->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteClothBuyItem($cloth_buy_item)
    {
        return $cloth_buy_item->delete();
    }
}
