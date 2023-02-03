<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Product;
use App\Models\Sewing;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SewingRepository implements Interfaces\iSewing
{
    use Common;

    /**
     * لیست دوخت ها
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getSewings($inputs): LengthAwarePaginator
    {
        try {
            return Sewing::query()
                ->with([
                    'product:id,name',
                    'seamstress:id,name,family',
                    'place:id,name',
                    'creator:id,person_id',
                    'creator.person:id,name,family'
                ])
                ->select([
                    'id',
                    'product_id',
                    'seamstress_person_id',
                    'place_id',
                    'is_mozdi_dooz',
                    'count',
                    'created_by',
                    'created_at'
                ])
                ->where('product_id', $inputs['product_id'])
                ->where(function ($q) use ($inputs) {
                    $q->whereHas('seamstress', function ($q2) use ($inputs) {
                        $q2->whereRaw($inputs['where']['person']['condition'], $inputs['where']['person']['params']);
                    })
                    ->orWhereHas('place', function ($q2) use ($inputs) {
                        $q2->whereRaw($inputs['where']['place']['condition'], $inputs['where']['place']['params']);
                    });
                })
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات دوخت
     * @param $inputs
     * @param array $select
     * @param array $relation
     * @return mixed
     * @throws ApiException
     */
    public function getSewingById($inputs, $select = [], $relation = []): mixed
    {
        try {
            $sewing = Sewing::where('product_id', $inputs['product_id'])
                ->where('id', $inputs['id']);

            if (count($relation)) {
                $sewing = $sewing->with($relation);
            }

            if (count($select)) {
                $sewing = $sewing->select($select);
            }

            return $sewing->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش دوخت
     * @param $sewing
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editSewing($sewing, $inputs): mixed
    {
        try {
            $sewing->product_id = $inputs['product_id'];
            if (isset($inputs['seamstress_person_id'])) {
                $sewing->seamstress_person_id = $inputs['seamstress_person_id'];
            }
            if (isset($inputs['place_id'])) {
                $sewing->place_id = $inputs['place_id'];
            }
            $sewing->color_id = $inputs['color_id'];
            $sewing->is_mozdi_dooz = $inputs['is_mozdi_dooz'];
            $sewing->count = $inputs['count'];
            if (isset($inputs['description'])) {
                $sewing->description = $inputs['description'];
            }

            return $sewing->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن دوخت
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addSewing($inputs, $user): array
    {
        try {
            $sewing = new Sewing();

            $sewing->product_id = $inputs['product_id'];
            $sewing->seamstress_person_id = $inputs['seamstress_person_id'] ?? null;
            $sewing->place_id = $inputs['place_id'] ?? null;
            $sewing->color_id = $inputs['color_id'];
            $sewing->is_mozdi_dooz = $inputs['is_mozdi_dooz'];
            $sewing->count = $inputs['count'];
            $sewing->description = $inputs['description'] ?? null;
            $sewing->created_by = $user->id;

            $result = $sewing->save();

            return [
                'result' => $result,
                'data' => $result ? $sewing->id : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف دوخت
     * @param $sewing
     * @return mixed
     * @throws ApiException
     */
    public function deleteSewing($sewing): mixed
    {
        try {
            return $sewing->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
