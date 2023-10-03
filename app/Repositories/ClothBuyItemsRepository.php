<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\ClothBuy;
use App\Models\ClothBuyItem;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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
                'warehouse_place:id,name',
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
            return ClothBuyItem::query()
                ->where('cloth_buy_id', $inputs['cloth_buy_id'])
                ->where('color_id', $inputs['color_id'])
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addClothBuyItem($inputs, $user, $quiet = false): array
    {
        try {
            $cloth_buy_item = new ClothBuyItem();

            $cloth_buy_item->cloth_buy_id = $inputs['cloth_buy_id'];
            $cloth_buy_item->color_id = $inputs['color_id'];
            $cloth_buy_item->metre = $inputs['metre'];
            $cloth_buy_item->unit_price = $inputs['price'];
            $cloth_buy_item->price = $inputs['price'] * $inputs['metre'];

            if (!$quiet) {
                $result = $cloth_buy_item->save();
            } else {
                $result = $cloth_buy_item->saveQuietly();
            }

            return [
                'result' => $result,
                'data' => $result ? $cloth_buy_item->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editClothBuyItem($inputs)
    {
        try {
            return ClothBuyItem::where('cloth_buy_id', $inputs['cloth_buy_id'])
                ->where('color_id', $inputs['color_id'])
                ->update([
                    'metre' => $inputs['metre'],
                    'unit_price' => $inputs['price'],
                    'price' => $inputs['price'] * $inputs['metre']
                ]);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteClothBuyItem($cloth_buy_item)
    {
        try {
            return $cloth_buy_item->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteClothBuyItems($cloth_buy_id)
    {
        try {
            return ClothBuyItem::where('cloth_buy_id', $cloth_buy_id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteClothBuyData($inputs)
    {
        try {
            return ClothBuyItem::where('cloth_buy_id', $inputs['cloth_buy_id'])
                ->where('color_id', $inputs['color_id'])
                ->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
