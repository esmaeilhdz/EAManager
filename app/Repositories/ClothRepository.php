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

    /**
     * @param $inputs
     * @param $user
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getClothes($inputs, $user): LengthAwarePaginator
    {
        $company_id = $this->getCurrentCompanyOfUser($user);
        try {
            return Cloth::with([
                'creator:id,person_id',
                'creator.person:id,name,family',
                'color:enum_id,enum_caption',
                'cloth_buy:cloth_id,receive_date,seller_place_id,warehouse_place_id,factor_no,price',
                'cloth_buy.seller_place:id,name',
                'cloth_buy.warehouse_place:id,name',
                'cloth_sell:cloth_id,sell_date,customer_id,warehouse_place_id,factor_no,price',
                'cloth_sell.customer:id,name',
                'cloth_sell.warehouse_place:id,name'
            ])
                ->select([
                    'id',
                    'code',
                    'name',
                    'created_by',
                    'created_at'
                ])
                ->where('company_id', $company_id)
                ->when(isset($inputs['search_txt']), function ($q) use ($inputs) {
                    $q->where('name', 'like', '%' . $inputs['search_txt'] . '%');
                })
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getClothByCode($code, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Cloth::select([
                'id',
                'code',
                'name',
            ])
                ->whereCode($code)
                ->where('company_id', $company_id)
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getClothCombo($inputs, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Cloth::select([
                'code',
                'name'
            ])
                ->when(isset($inputs['search_txt']), function ($q) use ($inputs) {
                    $q->where('name', 'like', '%' . $inputs['search_txt'] . '%');
                })
                ->where('company_id', $company_id)
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editCloth($cloth, $inputs)
    {
        try {
            $cloth->name = $inputs['name'];

            return $cloth->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addCloth($inputs, $user): array
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $cloth = new Cloth();

            $cloth->code = $this->randomString();
            $cloth->company_id = $company_id;
            $cloth->name = $inputs['name'];
            $cloth->created_by = $user->id;

            $result = $cloth->save();

            return [
                'result' => $result,
                'data' => $result ? $cloth : null
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
