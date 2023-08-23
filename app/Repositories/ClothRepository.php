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

    public function getClothes($inputs): LengthAwarePaginator
    {
        try {
            return Cloth::with([
                'creator:id,person_id',
                'creator.person:id,name,family',
                'color:enum_id,enum_caption',
                'cloth_buy:cloth_id',
                'cloth_sell:cloth_id'
            ])
                ->select([
                    'id',
                    'code',
                    'name',
                    'color_id',
                    'created_by',
                    'created_at'
                ])
                ->when(isset($inputs['search_txt']), function ($q) use ($inputs) {
                    $q->whereLike('name', $inputs['search_txt']);
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
