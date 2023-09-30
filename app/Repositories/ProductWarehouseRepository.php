<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Product;
use App\Models\ProductWarehouse;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductWarehouseRepository implements Interfaces\iProductWarehouse
{
    use Common;

    /**
     * لیست انبارهای کالای شرکت کاربر
     * @param $product_id
     * @param $inputs
     * @param $user
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getProductWarehouses($product_id, $inputs, $user): LengthAwarePaginator
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return ProductWarehouse::query()
                ->with([
                    'place:id,name',
                    'product_model:id,product_id,name',
                    'creator:id,person_id',
                    'creator.person:id,name,family'
                ])
                ->select([
                    'id',
                    'product_model_id',
                    'place_id',
                    'free_size_count',
                    'size1_count',
                    'size2_count',
                    'size3_count',
                    'size4_count',
                    'is_enable',
                    'created_by',
                    'created_at'
                ])
                ->where('product_id', $product_id)
                ->whereHas('place', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * جزئیات انبار کالا
     * @param $inputs
     * @param $user
     * @param array $select
     * @param array $relation
     * @return mixed
     * @throws ApiException
     */
    public function getProductWarehouseById($inputs, $user, $select = [], $relation = []): mixed
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);

            $product_warehouse = ProductWarehouse::where('id', $inputs['product_warehouse_id'] ?? $inputs['warehouse_id'])
                ->where('product_id', $inputs['product_id'])
                ->whereHas('place', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                });

            if (count($relation)) {
                $product_warehouse = $product_warehouse->with($relation);
            }

            if (count($select)) {
                $product_warehouse = $product_warehouse->select($select);
            }

            return $product_warehouse->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getDestinationProductWarehouseById($inputs, $select = [], $relation = [])
    {
        try {
            $product_warehouse = ProductWarehouse::where('id', $inputs['warehouse_id'])
                ->where('product_id', $inputs['product_id']);

            if (count($relation)) {
                $product_warehouse = $product_warehouse->with($relation);
            }

            if (count($select)) {
                $product_warehouse = $product_warehouse->select($select);
            }

            return $product_warehouse->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getByProductId($id, $select = [], $relation = [])
    {
        try {
            $product_warehouse = ProductWarehouse::where('product_id', $id);

            if (count($relation)) {
                $product_warehouse = $product_warehouse->with($relation);
            }

            if (count($select)) {
                $product_warehouse = $product_warehouse->select($select);
            }

            return $product_warehouse->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getById($id, $select = [], $relation = [])
    {
        try {
            $product_warehouse = ProductWarehouse::where('id', $id);

            if (count($relation)) {
                $product_warehouse = $product_warehouse->with($relation);
            }

            if (count($select)) {
                $product_warehouse = $product_warehouse->select($select);
            }

            return $product_warehouse->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getByStockProduct($inputs, $data, $select = [], $relation = [])
    {
        try {
            $product_warehouse = ProductWarehouse::where('product_id', $inputs['product_id'])
                ->where('free_size_count', '>=', $data['free_size_count'])
                ->where('size1_count', '>=', $data['size1_count'])
                ->where('size2_count', '>=', $data['size2_count'])
                ->where('size3_count', '>=', $data['size3_count'])
                ->where('size4_count', '>=', $data['size4_count'])
                ->whereHas('place', function ($q) use ($inputs) {
                    $q->where('company_id', $inputs['company_id']);
                });

            if (count($relation)) {
                $product_warehouse = $product_warehouse->with($relation);
            }

            if (count($select)) {
                $product_warehouse = $product_warehouse->select($select);
            }

            return $product_warehouse->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * محصولات براساس انبار
     * @param $inputs
     * @param $user
     * @return Collection|array
     * @throws ApiException
     */
    public function getByPlaceId($inputs, $user): Collection|array
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return ProductWarehouse::with([
                'product:id,name',
                'product_model:id,product_id,name'
            ])
                ->select('id', 'product_id', 'product_model_id')
                ->where('place_id', $inputs['id'])
                ->whereHas('product', function ($q) use ($inputs, $company_id) {
                    $q->when(isset($inputs['search_txt']), function ($q2) use ($inputs) {
                        $q2->where('name', 'like', '%' . $inputs['search_txt'] . '%');
                    })
                    ->where('company_id', $company_id);
                })
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getByProductAndPlace($place_id, $product_id, $product_model_id, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return ProductWarehouse::select('id')
                ->where('place_id', $place_id)
                ->where('product_id', $product_id)
                ->where('product_model_id', $product_model_id)
                ->whereHas('product', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getProductWarehouseCombo($inputs, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return ProductWarehouse::select('id', 'product_id', 'product_model_id', 'place_id')
                ->with([
                    'product:id,name',
                    'product_model:id,name',
                    'place:id,name',
                ])
                ->where(function ($q) use ($company_id, $inputs) {
                    $q->whereHas('product', function ($q2) use ($company_id, $inputs) {
                        $q2->where('company_id', $company_id);
                        $q2->when(isset($inputs['search_txt']), function ($q3) use ($inputs) {
                            $q3->where('name', 'like', '%' . $inputs['search_txt'] . '%');
                        });
                    })
                    ->orWhereHas('product_model', function ($q3) use ($company_id, $inputs) {
                        $q3->when(isset($inputs['search_txt']), function ($q4) use ($inputs) {
                            $q4->where('name', 'like', '%' . $inputs['search_txt'] . '%');
                        });
                    });
                })
                ->whereHas('product_model', function ($q) {
                    $q->where('is_enable', 1);
                })
                ->where('is_enable', 1)
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش انبار کالا
     * @param $product_warehouse
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editProductWarehouse($product_warehouse, $inputs): mixed
    {
        try {
            if (!empty($inputs['place_id'])) {
                $product_warehouse->place_id = $inputs['place_id'];
            }

            $product_warehouse->free_size_count = $inputs['free_size_count'];
            $product_warehouse->size1_count = $inputs['size1_count'];
            $product_warehouse->size2_count = $inputs['size2_count'];
            $product_warehouse->size3_count = $inputs['size3_count'];
            $product_warehouse->size4_count = $inputs['size4_count'];

            if (isset($inputs['is_enable'])) {
                $product_warehouse->is_enable = $inputs['is_enable'];
            }

            return $product_warehouse->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * غیرفعال سازی انبار یک کالا
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function deActiveOldWarehouses($inputs): mixed
    {
        try {
            return ProductWarehouse::where('product_id', $inputs['product_id'])
                ->where('product_model_id', $inputs['product_model_id'])
                ->where('place_id', $inputs['place_id'])
                ->update(['is_enable' => 0]);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن انبار کالا
     * @param $inputs
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addProductWarehouse($inputs, $user): array
    {
        try {
            $product_warehouse = new ProductWarehouse();

            $product_warehouse->product_id = $inputs['product_id'];
            $product_warehouse->product_model_id = $inputs['product_model_id'];
            $product_warehouse->place_id = $inputs['place_id'];
            $product_warehouse->free_size_count = $inputs['free_size_count'];
            $product_warehouse->size1_count = $inputs['size1_count'];
            $product_warehouse->size2_count = $inputs['size2_count'];
            $product_warehouse->size3_count = $inputs['size3_count'];
            $product_warehouse->size4_count = $inputs['size4_count'];
            $product_warehouse->created_by = $user->id;

            $result = $product_warehouse->save();

            return [
                'result' => $result,
                'data' => $result ? $product_warehouse->id : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف انبار کالا
     * @param $product_warehouse
     * @return mixed
     * @throws ApiException
     */
    public function deleteProductWarehouse($product_warehouse): mixed
    {
        try {
            return $product_warehouse->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
