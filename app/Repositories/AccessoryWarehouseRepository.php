<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Accessory;
use App\Models\AccessoryWareHouse;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AccessoryWarehouseRepository implements Interfaces\iAccessoryWarehouse
{
    use Common;

    /**
     * لیست خرید خرج کار ها
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getAccessoryWarehouses($inputs): LengthAwarePaginator
    {
        try {
            return AccessoryWareHouse::with([
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
     * @param $id
     * @return Builder|Builder[]|Collection|Model|null
     * @throws ApiException
     */
    public function getAccessoryWarehouseById($id): Model|Collection|Builder|array|null
    {
        try {
            return AccessoryWareHouse::with([
                'place:id,name'
            ])
                ->select([
                    'id',
                    'place_id',
                    'count'
                ])
                ->find($id);
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
    public function editAccessoryWarehouse($inputs): mixed
    {
        try {
            $accessory_warehouse = AccessoryWareHouse::where('accessory_id', $inputs['accessory_id'])->first();

            if ($inputs['sign'] == 'plus') {
                $accessory_warehouse->count += $inputs['count'];
            } elseif ($inputs['sign'] == 'minus') {
                $accessory_warehouse->count -= $inputs['count'];
            }

            return $accessory_warehouse->save();
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
    public function addAccessoryWarehouse($inputs, $user): array
    {
        try {
            $accessory_buy = new AccessoryWarehouse();

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
     * @param $id
     * @return mixed
     * @throws ApiException
     */
    public function deleteAccessoryWarehouse($id)
    {
        try {
            return Accessory::where('id', $id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
