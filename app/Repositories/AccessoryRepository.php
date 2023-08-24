<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Accessory;
use App\Models\AccessoryBuy;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AccessoryRepository implements Interfaces\iAccessory
{
    use Common;

    /**
     * لیست خرج کار ها
     * @param $inputs
     * @param $user
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getAccessories($inputs, $user): LengthAwarePaginator
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Accessory::with([
                'creator:id,person_id',
                'creator.person:id,name,family',
            ])
                ->select([
                    'id',
                    'name',
                    'is_enable',
                    'created_by',
                    'created_at',
                ])
                ->when(isset($inputs['search_txt']), function ($q) use ($inputs) {
                    $q->where('name', 'like', '%' . $inputs['search_txt'] . '%');
                })
                ->where('company_id', $company_id)
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات خرج کار
     * @param $id
     * @param array $select
     * @param array $relation
     * @return Builder|Builder[]|Collection|Model|null
     * @throws ApiException
     */
    public function getAccessoryById($id, $select = [], $relation = []): Model|Collection|Builder|array|null
    {
        try {
            $accessory = Accessory::where('id', $id);

            if (count($select)) {
                $accessory = $accessory->select($select);
            }

            if (count($relation)) {
                $accessory = $accessory->with($relation);
            }

            return $accessory->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش خرج کار
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editAccessory($inputs): mixed
    {
        try {
            return Accessory::where('id', $inputs['id'])
                ->update([
                    'name' => $inputs['name'],
                    'is_enable' => $inputs['is_enable']
                ]);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن خرج کار
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addAccessory($inputs, $user): array
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $accessory = new Accessory();

            $accessory->company_id = $company_id;
            $accessory->name = $inputs['name'];
            $accessory->created_by = $user->id;

            $result = $accessory->save();

            return [
                'result' => $result,
                'data' => $result ? $accessory->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف خرج کار
     * @param $id
     * @return mixed
     * @throws ApiException
     */
    public function deleteAccessory($id)
    {
        try {
            return Accessory::where('id', $id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function changeStatusAccessory($accessory, $inputs)
    {
        try {
            $accessory->is_enable = $inputs['is_enable'];

            return $accessory->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
