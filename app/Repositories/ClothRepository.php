<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Cloth;
use App\Models\ClothBuy;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClothRepository implements Interfaces\iCloth
{
    use Common;

    public function getClothes($inputs)
    {
        try {
            return ClothBuy::with([
                'creator:id,person_id',
                'creator.person:id,name,family',
                'cloth:id,code,color_id,name',
                'cloth.color:enum_id,enum_caption',
                'seller_place:id,name',
                'warehouse_place:id,name',
            ])
                ->select([
                    'id',
                    'cloth_id',
                    'seller_place_id',
                    'warehouse_place_id',
                    'metre',
                    'roll_count',
                    'receive_date',
                    'factor_no',
                    'price',
                    'created_by',
                    'created_at'
                ])
                ->whereHas('cloth', function ($q) use ($inputs) {
                    $q->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params']);
                })
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getClothByCode($code)
    {
        try {
            return Cloth::with([
                'color:enum_id,enum_caption'
            ])
                ->select([
                    'id',
                    'code',
                    'name',
                    'color_id'
                ])
                ->whereCode($code)
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editCloth($cloth, $inputs)
    {
        try {
            $cloth->name = $inputs['name'];
            $cloth->color_id = $inputs['color_id'];

            return $cloth->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addCloth($inputs, $user, $company_id): array
    {
        try {
            $cloth = new Cloth();

            $cloth->code = $this->randomString();
            $cloth->company_id = $company_id;
            $cloth->name = $inputs['name'];
            $cloth->color_id = $inputs['color_id_item'];
            $cloth->created_by = $user->id;

            $result = $cloth->save();

            return [
                'result' => $result,
                'data' => $result ? $cloth->code : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deleteCloth($code)
    {
        try {
            return Cloth::whereCode($code)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
