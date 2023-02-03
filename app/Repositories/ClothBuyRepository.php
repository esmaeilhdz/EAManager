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
                'place:id,name'
            ])
                ->select([
                    'id',
                    'cloth_id',
                    'place_id',
                    'metre',
                    'roll_count',
                    'receive_date',
                    'created_by',
                    'created_at'
                ])
                ->where('cloth_id', $inputs['cloth_id'])
                ->whereHas('place', function ($q) use ($inputs) {
                    $q->whereRaw($inputs['where']['place']['condition'], $inputs['where']['place']['params']);
                })
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
                'place:id,name'
            ])
                ->select([
                    'id',
                    'cloth_id',
                    'place_id',
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
            $cloth_buy->place_id = $inputs['place_id'];
            $cloth_buy->metre = $inputs['metre'];
            $cloth_buy->roll_count = $inputs['roll_count'];

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
            $cloth_buy->place_id = $inputs['place_id'];
            $cloth_buy->metre = $inputs['metre'];
            $cloth_buy->roll_count = $inputs['roll_count'];
            $cloth_buy->receive_date = $inputs['receive_date'];
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
