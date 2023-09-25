<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\ProductAccessory;
use App\Traits\Common;

class ProductAccessoryRepository implements Interfaces\iProductAccessory
{
    use Common;

    public function getProductAccessories($inputs, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return ProductAccessory::query()
                ->with([
                    'model'
                ])
                ->select('id', 'product_id', 'model_type', 'model_id', 'amount', 'created_at')
                ->whereHas('product', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->orderByDesc('id')
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e, false);
        }
    }

    public function getById($product_id, $id, $user, $relation = [])
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return ProductAccessory::query()
                ->with([
                    'model'
                ])
                ->select('id', 'product_id', 'model_type', 'model_id', 'amount')
                ->whereHas('product', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->where('product_id', $product_id)
                ->where('id', $id)
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e, false);
        }
    }

    public function getCombo($user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return ProductAccessory::with([
                'product:id,name'
            ])
                ->select('id', 'name', 'product_id')
                ->whereHas('product', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->where('is_enable', 1)
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e, false);
        }
    }

    public function editProductAccessory($product_accessory, $inputs)
    {
        try {
            $product_accessory->model_type = $inputs['model_type'];
            $product_accessory->model_id = $inputs['model_id'];
            $product_accessory->amount = $inputs['amount'];

            return $product_accessory->save();
        } catch (\Exception $e) {
            throw new ApiException($e, false);
        }
    }

    public function addProductAccessory($inputs, $user)
    {
        try {
            $product_accessory = new ProductAccessory();

            $product_accessory->product_id = $inputs['product_id'];
            $product_accessory->model_type = $inputs['model_type'];
            $product_accessory->model_id = $inputs['model_id'];
            $product_accessory->amount = $inputs['amount'];

            $result = $product_accessory->save();

            return [
                'result' => $result,
                'data' => $result ? $product_accessory->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e, false);
        }
    }

    public function deleteProductAccessories($product_id)
    {
        try {
            return ProductAccessory::where('product_id', $product_id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e, false);
        }
    }

    public function deleteProductAccessory($product_accessory)
    {
        try {
            return $product_accessory->delete();
        } catch (\Exception $e) {
            throw new ApiException($e, false);
        }
    }
}
