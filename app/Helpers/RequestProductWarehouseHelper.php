<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iProduct;
use App\Repositories\Interfaces\iProductToStore;
use App\Repositories\Interfaces\iProductWarehouse;
use App\Repositories\Interfaces\iRequestProductWarehouse;
use App\Traits\Common;
use App\Traits\RequestProductWarehouseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestProductWarehouseHelper
{
    use Common, RequestProductWarehouseTrait;

    // attributes
    public iProduct $product_interface;
    public iProductToStore $product_store_interface;
    public iProductWarehouse $product_warehouse_interface;
    public iRequestProductWarehouse $request_product_warehouse_interface;

    public function __construct(
        iProduct $product_interface,
        iProductToStore $product_store_interface,
        iProductWarehouse $product_warehouse_interface,
        iRequestProductWarehouse $request_product_warehouse_interface
    )
    {
        $this->product_interface = $product_interface;
        $this->product_store_interface = $product_store_interface;
        $this->product_warehouse_interface = $product_warehouse_interface;
        $this->request_product_warehouse_interface = $request_product_warehouse_interface;
    }

    /**
     * سرویس لیست درخواست های کالا از انبار
     * @param $inputs
     * @return array
     */
    public function getRequestProductWarehouses($inputs): array
    {
        $select = ['id'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['product_id'] = $product->id;
        $select = ['id'];
        $product_warehouse = $this->product_warehouse_interface->getProductWarehouseById($inputs, $select);
        if (is_null($product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name;family;national_code;concat_ws(" ",name,family);replace(concat_ws("",name,family)," ","")');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'request_product_from_warehouses');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $request_product_warehouses = $this->request_product_warehouse_interface->getRequestProductWarehouses($inputs);

        $request_product_warehouses->transform(function ($item) {
            return [
                'id' => $item->id,
                'request_user' => [
                    'full_name' => $item->request_user->person->name . ' ' . $item->request_user->person->family
                ],
                'confirm_user' => is_null($item->confirm_user_id) ? null : [
                    'full_name' => $item->confirm_user->person->name . ' ' . $item->confirm_user->person->family
                ],
                'free_size_count' => $item->free_size_count,
                'size1_count' => $item->size1_count,
                'size2_count' => $item->size2_count,
                'size3_count' => $item->size3_count,
                'size4_count' => $item->size4_count,
                'is_confirm' => $item->is_confirm,
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
            'data' => $request_product_warehouses
        ];
    }

    /**
     * سرویس جزئیات درخواست کالا از انبار
     * @param $inputs
     * @return array
     */
    public function getRequestProductWarehouseDetail($inputs): array
    {
        $select = ['id'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['product_id'] = $product->id;
        $select = ['id'];
        $product_warehouse = $this->product_warehouse_interface->getProductWarehouseById($inputs, $select);
        if (is_null($product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['product_warehouse_id'] = $product_warehouse->id;
        $select = ['request_user_id', 'confirm_user_id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count', 'is_confirm'];
        $relation = [
            'request_user:id,person_id',
            'request_user.person:id,name,family',
            'confirm_user:id,person_id',
            'confirm_user.person:id,name,family',
        ];
        $request_product_warehouse = $this->request_product_warehouse_interface->getRequestProductWarehouseById($inputs, $select, $relation);
        if (is_null($request_product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $request_product_warehouse
        ];
    }

    /**
     * سرویس ویرایش درخواست کالا از انبار
     * @param $inputs
     * @return array
     */
    public function editRequestProductWarehouse($inputs): array
    {
        $select = ['id'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['product_id'] = $product->id;
        $select = ['id'];
        $product_warehouse = $this->product_warehouse_interface->getProductWarehouseById($inputs, $select);
        if (is_null($product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $select = ['id', 'is_confirm'];
        $inputs['product_warehouse_id'] = $product_warehouse->id;
        $request_product_warehouse = $this->request_product_warehouse_interface->getRequestProductWarehouseById($inputs, $select);
        if (is_null($request_product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // پس از تایید درخواست، ویرایش انجام نخواهد شد.
        if ($request_product_warehouse->is_confirm == 1) {
            return [
                'result' => false,
                'message' => __('messages.request_confirmed_not_allowed'),
                'data' => null
            ];
        }

        $result = $this->request_product_warehouse_interface->editRequestProductWarehouse($request_product_warehouse, $inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * سرویس تایید درخواست کالا از انبار
     * @param $inputs
     * @return array
     */
    public function confirmRequestProductWarehouse($inputs): array
    {
        // محصول
        $select = ['id'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // انبار مبدا
        $inputs['product_id'] = $product->id;
        $select = ['id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count'];
        $product_warehouse = $this->product_warehouse_interface->getProductWarehouseById($inputs, $select);
        if (is_null($product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // انبار مقصد
        $inputs['product_id'] = $product->id;
        $select = ['id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count'];
        $destination_product_warehouse = $this->product_warehouse_interface->getDestinationProductWarehouseById($inputs, $select);
        if (is_null($destination_product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // درخواست محصول از انبار
        $select = ['id', 'is_confirm', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count'];
        $inputs['product_warehouse_id'] = $product_warehouse->id;
        $request_product_warehouse = $this->request_product_warehouse_interface->getRequestProductWarehouseById($inputs, $select);
        if (is_null($request_product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $user = Auth::user();
        $inputs = [
            'product_warehouse_id' => $destination_product_warehouse->id,
            'free_size_count' => $request_product_warehouse->free_size_count,
            'size1_count' => $request_product_warehouse->size1_count,
            'size2_count' => $request_product_warehouse->size2_count,
            'size3_count' => $request_product_warehouse->size3_count,
            'size4_count' => $request_product_warehouse->size4_count,
            'description' => 'ثبت شده از تایید درخواست از انبار توسط ' . $user->person->name . ' ' . $user->person->family,
        ];

        DB::beginTransaction();
        $result[] = $this->request_product_warehouse_interface->confirmRequestProductWarehouse($request_product_warehouse, $user);
        $result[] = $this->product_store_interface->addProductToStore($inputs, $user, true)['result'];

        $result_calculate_product_warehouse = $this->calculateForProductWarehouse($product_warehouse, $request_product_warehouse);
        if (!$result_calculate_product_warehouse['result']) {
            DB::rollBack();
            return $result_calculate_product_warehouse;
        }

        $result[] = $this->product_warehouse_interface->editProductWarehouse($product_warehouse, $result_calculate_product_warehouse['data']['inputs_product_warehouse']);
        $result[] = $this->product_warehouse_interface->editProductWarehouse($destination_product_warehouse, $result_calculate_product_warehouse['data']['inputs_request_product_warehouse']);

        if (!in_array(false, $result)) {
            $flag = true;
            DB::commit();
        } else {
            $flag = false;
            DB::rollBack();
        }

        return [
            'result' => $flag,
            'message' => $flag ? __('messages.success') : $message ?? __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * سرویس افزودن درخواست کالا از انبار
     * @param $inputs
     * @return array
     */
    public function addRequestProductWarehouse($inputs): array
    {
        $select = ['id'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['product_id'] = $product->id;
        $select = ['id'];
        $product_warehouse = $this->product_warehouse_interface->getProductWarehouseById($inputs, $select);
        if (is_null($product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $user = Auth::user();
        $result = $this->request_product_warehouse_interface->addRequestProductWarehouse($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * سرویس حذف درخواست کالا از انبار
     * @param $inputs
     * @return array
     */
    public function deleteRequestProductWarehouse($inputs): array
    {
        $select = ['id'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['product_id'] = $product->id;
        $select = ['id'];
        $product_warehouse = $this->product_warehouse_interface->getProductWarehouseById($inputs, $select);
        if (is_null($product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $select = ['id', 'is_confirm'];
        $inputs['product_warehouse_id'] = $product_warehouse->id;
        $request_product_warehouse = $this->request_product_warehouse_interface->getRequestProductWarehouseById($inputs, $select);
        if (is_null($request_product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // پس از تایید درخواست، حذف انجام نخواهد شد.
        if ($request_product_warehouse->is_confirm == 1) {
            return [
                'result' => false,
                'message' => __('messages.request_confirmed_not_allowed'),
                'data' => null
            ];
        }

        $result = $this->request_product_warehouse_interface->deleteRequestProductWarehouse($request_product_warehouse);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }


}
