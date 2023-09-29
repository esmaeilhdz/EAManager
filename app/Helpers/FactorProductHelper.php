<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\Repositories\Interfaces\iCustomer;
use App\Repositories\Interfaces\iFactor;
use App\Repositories\Interfaces\iFactorPayment;
use App\Repositories\Interfaces\iFactorProduct;
use App\Repositories\Interfaces\iProductWarehouse;
use App\Repositories\Interfaces\iRequestProductWarehouse;
use App\Traits\Common;
use App\Traits\FactorTrait;
use App\Traits\RequestProductWarehouseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FactorProductHelper
{
    use Common, RequestProductWarehouseTrait, FactorTrait;

    // فاکتور ناقص
    const InCompleteFactor = 1;
    // فاکتور تایید شده
    const ConfirmFactor = 2;
    // فاکتور مرجوعی
    const ReturnedFactor = 3;

    // attributes
    public iFactor $factor_interface;
    public iFactorProduct $factor_product_interface;
    public iProductWarehouse $product_warehouse_interface;
    public iRequestProductWarehouse $request_product_interface;

    public function __construct(
        iFactor           $factor_interface,
        iFactorProduct    $factor_product_interface,
        iProductWarehouse $product_warehouse_interface,
    )
    {
        $this->factor_interface = $factor_interface;
        $this->factor_product_interface = $factor_product_interface;
        $this->product_warehouse_interface = $product_warehouse_interface;
    }

    /**
     * لیست کالاهای فاکتور
     * @param $inputs
     * @return array
     */
    public function getFactorProducts($inputs): array
    {
        $inputs['order_by'] = $this->orderBy($inputs, 'factor_products');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $user = Auth::user();
        $factor = $this->factor_interface->getFactorByCode($inputs['code'], $user);
        if (is_null($factor)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $select = ['id', 'factor_id', 'product_warehouse_id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count', 'price'];
        $relation = [
            'product_warehouse:id,place_id,product_id',
            'product_warehouse.place:id,name',
            'product_warehouse.product:id,code,name',
            'product_warehouse.product.factor_model:id,factor_id,name',
        ];
        $factor_products = $this->factor_product_interface->getByFactorId($factor->id, $select, $relation);

        $factor_products->transform(function ($item) {
            return [
                'id' => $item->id,
                'product' => [
                    'code' => $item->product_warehouse->product->code,
                    'name' => $item->product_warehouse->product->name . ' - ' . $item->product_warehouse->product->product_model->name,
                ],
                'place' => [
                    'id' => $item->product_warehouse->place->id,
                    'name' => $item->product_warehouse->place->name,
                ],
                'free_size_count' => $item->free_size_count,
                'size1_count' => $item->size1_count,
                'size2_count' => $item->size2_count,
                'size3_count' => $item->size3_count,
                'size4_count' => $item->size4_count,
                'price' => $item->price,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $factor_products
        ];
    }

    /**
     * جزئیات کالای فاکتور
     * @param $inputs
     * @return array
     */
    public function getFactorProductDetail($inputs): array
    {
        $user = Auth::user();
        $factor = $this->factor_interface->getFactorByCode($inputs['code'], $user);
        if (is_null($factor)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $select = ['id', 'factor_id', 'product_warehouse_id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count', 'price'];
        $relation = [
            'product_warehouse:id,place_id,product_id',
            'product_warehouse.place:id,name',
            'product_warehouse.product:id,code,name',
        ];
        $factor_product = $this->factor_product_interface->getById($factor->id, $inputs['id'], $select, $relation);

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $factor_product
        ];
    }

    /**
     * افزودن کالای فاکتور
     * @param $inputs
     * @return array
     */
    public function addFactorProduct($inputs): array
    {
        $user = Auth::user();
        $factor = $this->factor_interface->getFactorByCode($inputs['code'], $user);
        if (is_null($factor)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // برای افزودن کالا به فاکتور، فاکتور نباید تایید نهایی شده باشد.
        if ($factor->status == self::ConfirmFactor) {
            return [
                'result' => false,
                'message' => __('messages.factor_already_confirmed_cannot_add_product'),
                'data' => null
            ];
        }

        // برای افزودن کالا به فاکتور، فاکتور نباید مرجوع شده باشد.
        if ($factor->status == self::ReturnedFactor) {
            return [
                'result' => false,
                'message' => __('messages.factor_already_returned_cannot_add_product'),
                'data' => null
            ];
        }

        $product_warehouse = $this->product_warehouse_interface->getById($inputs['product_warehouse_id'], ['id']);
        if (is_null($product_warehouse)) {
            return [
                'result' => false,
                'message' => __('messages.product_warehouse_not_found'),
                'data' => null
            ];
        }

        $res_factor = $this->factor_product_interface->addFactorProduct($inputs, $factor->id, $user);
        $result = $res_factor['result'];

        return [
            'result' => $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => $result ? $res_factor['data'] : null
        ];
    }

    /**
     * حذف کالاهای فاکتور
     * @param $inputs
     * @return array
     */
    public function deleteFactorProducts($inputs): array
    {
        $user = Auth::user();
        $factor = $this->factor_interface->getFactorByCode($inputs['code'], $user);
        if (is_null($factor)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = (bool)$this->factor_product_interface->deleteFactorProducts($factor->id);

        return [
            'result' => $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * حذف کالای فاکتور
     * @param $inputs
     * @return array
     */
    public function deleteFactorProduct($inputs): array
    {
        $user = Auth::user();
        $factor = $this->factor_interface->getFactorByCode($inputs['code'], $user);
        if (is_null($factor)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = (bool) $this->factor_product_interface->deleteFactorProduct($factor->id, $inputs['id']);

        return [
            'result' => $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
