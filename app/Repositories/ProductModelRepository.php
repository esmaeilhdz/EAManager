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
                ->with([
                    'warehouse:model_type,model_id,free_size_count,size1_count,size2_count,size3_count,size4_count'
                ])
                ->select('id', 'name', 'pack_count', 'created_at')
                ->whereHas('product', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->where('product_id', $inputs['product_id'])
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
            return ProductModel::query()
                ->with([
                    'warehouse:model_type,model_id,free_size_count,size1_count,size2_count,size3_count,size4_count'
                ])
                ->select('id', 'name', 'pack_count', 'is_enable')
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

    public function getProductsModelCombo($inputs, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return ProductModel::with([
                'product:id,name'
            ])
                ->select('id', 'name', 'pack_count', 'product_id')
                ->whereHas('product', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->where('is_enable', 1)
                ->where('product_id', $inputs['product_id'])
                ->when(isset($inputs['search_txt']), function ($q) use ($inputs) {
                    $q->where('name', 'like', '%' . $inputs['search_txt'] . '%');
                })
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e, false);
        }
    }

    public function getProductModelCombo($inputs, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return ProductModel::select('id', 'name', 'pack_count')
                ->whereHas('product', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->where('product_id', $inputs['product_id'])
                ->where('is_enable', 1)
                ->when(isset($inputs['search_txt']), function ($q) use ($inputs) {
                    $q->where('name', 'like', '%' . $inputs['search_txt'] . '%');
                })
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e, false);
        }
    }

    public function editProductModel($product_model, $inputs)
    {
        try {
            $product_model->name = $inputs['name'];
            $product_model->pack_count = $inputs['pack_count'];
            $product_model->is_enable = $inputs['is_enable'];

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
            $product_model->pack_count = $inputs['pack_count'];

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
