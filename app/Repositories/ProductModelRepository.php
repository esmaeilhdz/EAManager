<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Product;
use App\Models\ProductModel;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductModelRepository implements Interfaces\iProductModel
{
    use Common;

    public function getProductModels($inputs, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return ProductModel::query()
                ->select('id', 'name', 'created_at')
                ->whereHas('product', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->orderByDesc('id')
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e, false);
        }
    }

    public function getById($product_id, $id, $user, $select = [], $relation = [])
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return ProductModel::query()
                ->select('id', 'name')
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

    public function editProductModel($product_model, $inputs)
    {
        try {
            $product_model->name = $inputs['name'];
            return $product_model->save();
        } catch (\Exception $e) {
            throw new ApiException($e, false);
        }
    }

    public function addProductModel($inputs, $user)
    {
        try {
            $product_model = new ProductModel();

            $product_model->product_id = $inputs['product_id'];
            $product_model->name = $inputs['name'];

            $result = $product_model->save();

            return [
                'result' => $result,
                'data' => $result ? $product_model->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e, false);
        }
    }

    public function deleteProductModel($product_model)
    {
        try {
            return $product_model->delete();
        } catch (\Exception $e) {
            throw new ApiException($e, false);
        }
    }
}
