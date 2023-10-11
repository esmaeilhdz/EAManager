<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\ClothSell;
use App\Models\ClothSellItem;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ClothSellItemsRepository implements Interfaces\iClothSellItems
{
    use Common;

    /**
     * لیست فروش پارچه
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getClothSellItems($inputs): LengthAwarePaginator
    {
        try {
            return ClothSell::with([
                'cloth:id,name',
                'seller_place:id,name',
                'warehouse_place:id,name',
                'items:cloth_sell_id,color_id,metre,unit_price,price',
                'items.color:enum_id,enum_caption'
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
     * جزئیات فروش پارچه
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function getClothSellItemById($inputs): mixed
    {
        try {
            return ClothSellItem::query()
                ->where('cloth_sell_id', $inputs['cloth_sell_id'])
                ->where('color_id', $inputs['color_id'])
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addClothSellItem($inputs, $user, $quiet = false): array
    {
        try {
            $cloth_sell_item = new ClothSellItem();

            $cloth_sell_item->cloth_sell_id = $inputs['cloth_sell_id'];
            $cloth_sell_item->color_id = $inputs['color_id'];
            $cloth_sell_item->metre = $inputs['metre'];
            $cloth_sell_item->unit_price = $inputs['price'];
            $cloth_sell_item->price = $inputs['price'] * $inputs['metre'];

            if (!$quiet) {
                $result = $cloth_sell_item->save();
            } else {
                $result = $cloth_sell_item->saveQuietly();
            }

            return [
                'result' => $result,
                'data' => $result ? $cloth_sell_item->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editClothSellItem($inputs)
    {
        try {
            return ClothSellItem::where('cloth_sell_id', $inputs['cloth_sell_id'])
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

    public function deleteClothSellItem($cloth_sell_item)
    {
        try {
            return $cloth_sell_item->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteClothSellItems($cloth_sell_id)
    {
        try {
            return ClothSellItem::where('cloth_sell_id', $cloth_sell_id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteClothSellData($inputs)
    {
        try {
            return ClothSellItem::where('cloth_sell_id', $inputs['cloth_sell_id'])
                ->where('color_id', $inputs['color_id'])
                ->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
