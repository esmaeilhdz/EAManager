<?php

namespace App\Traits;


use App\Exceptions\ApiException;
use App\Models\AccessoryBuy;
use App\Models\ProductAccessory;

trait ProductAccessoryTrait
{
    use Common;

    public function getInsertableAccessories($product_id, $user): array
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return ProductAccessory::select([
                    'id',
                    'accessory_id',
                ])
                ->whereHas('accessory', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->where('accessory_id', $inputs['accessory_id'])
                ->orderByRaw($inputs['order_by'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getDeleteAbleAccessoryIds($product_id): array
    {

    }

}
