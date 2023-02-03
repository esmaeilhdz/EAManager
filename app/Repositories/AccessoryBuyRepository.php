<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\AccessoryBuy;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AccessoryBuyRepository implements Interfaces\iAccessoryBuy
{
    use Common;

    /**
     * لیست خرید خرج کار ها
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getAccessoryBuys($inputs): LengthAwarePaginator
    {
        try {
            return AccessoryBuy::with([
                'creator:id,person_id',
                'creator.person:id,name,family',
                'place:id,name'
            ])
                ->select([
                    'id',
                    'accessory_id',
                    'place_id',
                    'count',
                    'created_by',
                    'created_at'
                ])
                ->whereHas('place', function ($q) use ($inputs) {
                    $q->whereRaw($inputs['where']['place']['condition'], $inputs['where']['place']['params']);
                })
                ->where('accessory_id', $inputs['accessory_id'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات خرید خرج کار
     * @param $inputs
     * @return Builder|Builder[]|Collection|Model|null
     * @throws ApiException
     */
    public function getAccessoryBuyById($inputs): Model|Collection|Builder|array|null
    {
        try {
            return AccessoryBuy::with([
                'place:id,name'
            ])
                ->select([
                    'id',
                    'accessory_id',
                    'place_id',
                    'count'
                ])
                ->where('accessory_id', $inputs['accessory_id'])
                ->where('id', $inputs['id'])
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش خرید خرج کار
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editAccessoryBuy($inputs): mixed
    {
        try {
            return AccessoryBuy::where('id', $inputs['id'])
                ->where('accessory_id', $inputs['accessory_id'])
                ->update([
                    'place_id' => $inputs['place_id'],
                    'count' => $inputs['count']
                ]);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن خرید خرج کار
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addAccessoryBuy($inputs, $user): array
    {
        try {
            $accessory_buy = new AccessoryBuy();

            $accessory_buy->accessory_id = $inputs['accessory_id'];
            $accessory_buy->place_id = $inputs['place_id'];
            $accessory_buy->count = $inputs['count'];
            $accessory_buy->created_by = $user->id;

            $result = $accessory_buy->save();

            return [
                'result' => $result,
                'data' => $result ? $accessory_buy->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف خرید خرج کار
     * @param $accessory_buy
     * @return mixed
     * @throws ApiException
     */
    public function deleteAccessoryBuy($accessory_buy): mixed
    {
        try {
            return $accessory_buy->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
