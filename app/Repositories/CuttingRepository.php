<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Product;
use App\Models\Cutting;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CuttingRepository implements Interfaces\iCutting
{
    use Common;

    /**
     * لیست برش ها
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getCuttings($inputs): LengthAwarePaginator
    {
        try {
            return Cutting::with([
                'creator:id,person_id',
                'creator.person:id,name,family'
            ])
                ->select([
                    'id',
                    'product_id',
                    'cutted_count',
                    'free_size_count',
                    'size1_count',
                    'size2_count',
                    'size3_count',
                    'size4_count',
                    'created_by',
                    'created_at'
                ])
                ->where('product_id', $inputs['product_id'])
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات برش
     * @param $inputs
     * @param array $select
     * @param array $relation
     * @return mixed
     * @throws ApiException
     */
    public function getCuttingById($inputs, $select = [], $relation = []): mixed
    {
        try {
            $cutting = Cutting::where('product_id', $inputs['product_id'])
                ->where('id', $inputs['id']);

            if (count($relation)) {
                $cutting = $cutting->with($relation);
            }

            if (count($select)) {
                $cutting = $cutting->select($select);
            }

            return $cutting->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش برش
     * @param $cutting
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editCutting($cutting, $inputs): mixed
    {
        try {
            $cutting->product_id = $inputs['product_id'];
            $cutting->cutted_count = $inputs['cutted_count'];
            $cutting->free_size_count = $inputs['free_size_count'];
            $cutting->size1_count = $inputs['size1_count'];
            $cutting->size2_count = $inputs['size2_count'];
            $cutting->size3_count = $inputs['size3_count'];
            $cutting->size4_count = $inputs['size4_count'];

            return $cutting->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن برش
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addCutting($inputs, $user): array
    {
        try {
            $cutting = new Cutting();

            $cutting->product_id = $inputs['product_id'];
            $cutting->cutted_count = $inputs['cutted_count'];
            $cutting->free_size_count = $inputs['free_size_count'];
            $cutting->size1_count = $inputs['size1_count'];
            $cutting->size2_count = $inputs['size2_count'];
            $cutting->size3_count = $inputs['size3_count'];
            $cutting->size4_count = $inputs['size4_count'];
            $cutting->created_by = $user->id;

            $result = $cutting->save();

            return [
                'result' => $result,
                'data' => $result ? $cutting->id : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف برش
     * @param $cutting
     * @return mixed
     * @throws ApiException
     */
    public function deleteCutting($cutting): mixed
    {
        try {
            return $cutting->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
