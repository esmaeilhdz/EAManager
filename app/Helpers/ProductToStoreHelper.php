<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iPlace;
use App\Repositories\Interfaces\iProduct;
use App\Repositories\Interfaces\iProductToStore;
use App\Repositories\Interfaces\iProductWarehouse;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductToStoreHelper
{
    use Common;

    // attributes
    public iPlace $place_interface;
    public iProduct $product_interface;
    public iProductToStore $product_to_store_interface;
    public iProductWarehouse $product_warehouse_interface;

    public function __construct(
        iProduct $product_interface,
        iProductToStore $product_to_store_interface,
        iProductWarehouse $product_warehouse_interface,
        iPlace $place_interface
    )
    {
        $this->place_interface = $place_interface;
        $this->product_interface = $product_interface;
        $this->product_to_store_interface = $product_to_store_interface;
        $this->product_warehouse_interface = $product_warehouse_interface;
    }

    /**
     * سرویس لیست ارسال کالا به فروشگاه
     * @param $inputs
     * @return array
     */
    public function getProductToStores($inputs): array
    {
        $select = ['id', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name');
        $inputs['where']['place']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['place']['params'] = $param_array;

        $select = ['id'];
        $product_warehouse = $this->product_warehouse_interface->getByProductId($product->id, $select);
        if (is_null($product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['order_by'] = $this->orderBy($inputs, 'product_to_stores');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $product_to_stores = $this->product_to_store_interface->getProductToStores($product_warehouse, $inputs);

        $product_to_stores->transform(function ($item) {
            return [
                'id' => $item->id,
                'place' => [
                    'name' => $item->productWarehouse->place->name
                ],
                'free_size_count' => $item->free_size_count,
                'size1_count' => $item->size1_count,
                'size2_count' => $item->size2_count,
                'size3_count' => $item->size3_count,
                'size4_count' => $item->size4_count,
                'total_count' => $item->free_size_count + $item->size1_count + $item->size2_count + $item->size3_count + $item->size4_count,
                'creator' => is_null($item->creator->person) ? null : [
                    'person' => [
                        'full_name' => $item->creator->person->name . ' ' . $item->creator->person->family,
                    ]
                ],
                'created_at' => $item->created_at,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $product_to_stores,
            'other' => [
                'product_name' => $product->name
            ]
        ];
    }

    /**
     * سرویس جزئیات ارسال کالا به فروشگاه
     * @param $code
     * @return array
     */
    public function getProductToStoreDetail($inputs): array
    {
        // کالا
        $select = ['id', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // انبار کالا جهت فیلتر در ارسال به فروشگاه
        $select = ['id', 'place_id'];
        $product_warehouse = $this->product_warehouse_interface->getByProductId($product->id, $select);
        if (is_null($product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // ارسال به فروشگاه
        $select = ['product_warehouse_id', 'place_id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count', 'description'];
        $product_to_store = $this->product_to_store_interface->getProductToStoreById($product_warehouse->id, $inputs['id'], $select);
        if (is_null($product_to_store)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $product_to_store
        ];
    }

    /**
     * سرویس ویرایش ارسال کالا به فروشگاه
     * @param $inputs
     * @return array
     */
    public function editProductToStore($inputs): array
    {
        // کالا
        $select = ['id', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // انبار کالا جهت فیلتر در ارسال به فروشگاه
        $select = ['id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count'];
        $product_warehouse = $this->product_warehouse_interface->getByProductId($product->id, $select);
        if (is_null($product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $select = ['id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count'];
        $product_to_store = $this->product_to_store_interface->getProductToStoreById($product_warehouse->id, $inputs['id'], $select);
        if (is_null($product_to_store)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        if ($inputs['free_size_count'] > $product_to_store->free_size_count) {
            $params['free_size_count'] = $product_warehouse->free_size_count - ($inputs['free_size_count'] - $product_to_store->free_size_count);
            if ($params['free_size_count'] < 0) {
                return [
                    'result' => false,
                    'message' => __('messages.free_size_not_enough'),
                    'data' => null
                ];
            }
        } elseif ($inputs['free_size_count'] < $product_to_store->free_size_count) {
            $params['free_size_count'] = $product_warehouse->free_size_count + ($product_to_store->free_size_count - $inputs['free_size_count']);
        } else {
            $params['free_size_count'] = $product_warehouse->free_size_count;
        }

        if ($inputs['size1_count'] > $product_to_store->size1_count) {
            $params['size1_count'] = $product_warehouse->size1_count - ($inputs['size1_count'] - $product_to_store->size1_count);
            if ($params['size1_count'] < 0) {
                return [
                    'result' => false,
                    'message' => __('messages.size1_not_enough'),
                    'data' => null
                ];
            }
        } elseif ($inputs['size1_count'] < $product_to_store->size1_count) {
            $params['size1_count'] = $product_warehouse->size1_count + ($product_to_store->size1_count - $inputs['size1_count']);
        } else {
            $params['size1_count'] = $product_warehouse->size1_count;
        }

        if ($inputs['size2_count'] > $product_to_store->size2_count) {
            $params['size2_count'] = $product_warehouse->size2_count - ($inputs['size2_count'] - $product_to_store->size2_count);
            if ($params['size2_count'] < 0) {
                return [
                    'result' => false,
                    'message' => __('messages.size2_not_enough'),
                    'data' => null
                ];
            }
        } elseif ($inputs['size2_count'] < $product_to_store->size2_count) {
            $params['size2_count'] = $product_warehouse->size2_count + ($product_to_store->size2_count - $inputs['size2_count']);
        } else {
            $params['size2_count'] = $product_warehouse->size2_count;
        }

        if ($inputs['size3_count'] > $product_to_store->size3_count) {
            $params['size3_count'] = $product_warehouse->size3_count - ($inputs['size3_count'] - $product_to_store->size3_count);
            if ($params['size3_count'] < 0) {
                return [
                    'result' => false,
                    'message' => __('messages.size3_not_enough'),
                    'data' => null
                ];
            }
        } elseif ($inputs['size3_count'] < $product_to_store->size3_count) {
            $params['size3_count'] = $product_warehouse->size3_count + ($product_to_store->size3_count - $inputs['size3_count']);
        } else {
            $params['size3_count'] = $product_warehouse->size3_count;
        }

        if ($inputs['size4_count'] > $product_to_store->size4_count) {
            $params['size4_count'] = $product_warehouse->size4_count - ($inputs['size4_count'] - $product_to_store->size4_count);
            if ($params['size4_count'] < 0) {
                return [
                    'result' => false,
                    'message' => __('messages.size4_not_enough'),
                    'data' => null
                ];
            }
        } elseif ($inputs['size4_count'] < $product_to_store->size4_count) {
            $params['size4_count'] = $product_warehouse->size4_count + ($product_to_store->size4_count - $inputs['size4_count']);
        } else {
            $params['size4_count'] = $product_warehouse->size4_count;
        }

        DB::beginTransaction();
        $result[] = $this->product_to_store_interface->editProductToStore($product_to_store, $inputs);
        $result[] = $this->product_warehouse_interface->editProductWarehouse($product_warehouse, $params);

        if (!in_array(false, $result)) {
            $flag = true;
            DB::commit();
        } else {
            $flag = false;
            DB::rollBack();
        }

        return [
            'result' => $flag,
            'message' => $flag ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * سرویس افزودن ارسال کالا به فروشگاه
     * @param $inputs
     * @return array
     */
    public function addProductToStore($inputs): array
    {
        // کالا
        $select = ['id', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // انبار کالا جهت فیلتر در ارسال به فروشگاه
        $select = ['id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count'];
        $product_warehouse = $this->product_warehouse_interface->getByProductId($product->id, $select);
        if (is_null($product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['product_warehouse_id'] = $product_warehouse->id;
        $user = Auth::user();
        $result = $this->product_to_store_interface->addProductToStore($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * سرویس حذف ارسال کالا به فروشگاه
     * @param $inputs
     * @return array
     */
    public function deleteProductToStore($inputs): array
    {
        // کالا
        $select = ['id', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // انبار کالا جهت فیلتر در ارسال به فروشگاه
        $select = ['id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count'];
        $product_warehouse = $this->product_warehouse_interface->getByProductId($product->id, $select);
        if (is_null($product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $select = ['id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count'];
        $product_to_store = $this->product_to_store_interface->getProductToStoreById($product_warehouse->id, $inputs['id'], $select);
        if (is_null($product_to_store)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $params['free_size_count'] = $product_to_store->free_size_count + $product_warehouse->free_size_count;
        $params['size1_count'] = $product_to_store->size1_count + $product_warehouse->size1_count;
        $params['size2_count'] = $product_to_store->size2_count + $product_warehouse->size2_count;
        $params['size3_count'] = $product_to_store->size3_count + $product_warehouse->size3_count;
        $params['size4_count'] = $product_to_store->size4_count + $product_warehouse->size4_count;

        DB::beginTransaction();
        $result[] = $this->product_to_store_interface->deleteProductToStore($product_to_store);
        $result[] = $this->product_warehouse_interface->editProductWarehouse($product_warehouse, $params);

        if (!in_array(false, $result)) {
            $flag = true;
            DB::commit();
        } else {
            $flag = false;
            DB::rollBack();
        }

        return [
            'result' => $flag,
            'message' => $flag ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }


}
