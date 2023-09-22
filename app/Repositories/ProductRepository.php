<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Product;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductRepository implements Interfaces\iProduct
{
    use Common;

    /**
     * لیست کالاها
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getProducts($inputs): LengthAwarePaginator
    {
        try {
            return Product::query()
                ->with([
                    'productWarehouse:product_id,free_size_count,size1_count,size2_count,size3_count,size4_count',
                    'productPrice:product_id,final_price',
                    'cloth:id,code,name',
                    'sale_period:id,name',
                    'creator:id,person_id',
                    'creator.person:id,name,family'
                ])
                ->select([
                    'id',
                    'code',
                    'internal_code',
                    'name',
                    'cloth_id',
                    'sale_period_id',
                    'created_by',
                    'created_at'
                ])
                ->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                ->paginate($inputs['per_page']);

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات کالا
     * @param $code
     * @param $user
     * @param array $select
     * @return mixed
     * @throws ApiException
     */
    public function getProductByCode($code, $user, $select = []): mixed
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $product = Product::with([
                'cloth:id,code,name',
                'cloth.color:enum_id,enum_caption',
                'sale_period:id,name',
            ]);

            if (count($select)) {
                $product = $product->select($select);
            }

            return $product->where('company_id', $company_id)
                ->whereCode($code)
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getProductsCombo($inputs, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Product::select('id', 'name')
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

    /**
     * ویرایش کالا
     * @param $product
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editProduct($product, $inputs): mixed
    {
        try {
            $product->internal_code = $inputs['internal_code'];
            $product->cloth_id = $inputs['cloth_id'];
            $product->sale_period_id = $inputs['sale_period_id'];
            $product->name = $inputs['name'];

            return $product->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن کالا
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addProduct($inputs, $user): array
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $product = new Product();

            $product->code = $this->randomString();
            $product->internal_code = $this->randomProductCode(10);
            $product->company_id = $company_id;
            $product->name = $inputs['name'];
            $product->cloth_id = $inputs['cloth_id'];
            $product->sale_period_id = $inputs['sale_period_id'];
            $product->created_by = $user->id;

            $result = $product->save();

            return [
                'result' => $result,
                'data' => $result ? $product->code : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف کالا
     * @param $product
     * @return mixed
     * @throws ApiException
     */
    public function deleteProduct($product): mixed
    {
        try {
            return $product->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
