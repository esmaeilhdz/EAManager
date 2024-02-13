<?php

namespace App\Helpers;

use App\Models\Accessory;
use App\Models\Cloth;
use App\Models\Product;
use App\Repositories\Interfaces\iFactor;
use App\Repositories\Interfaces\iFactorItem;
use App\Repositories\Interfaces\iProductWarehouse;
use App\Repositories\Interfaces\iRequestProductWarehouse;
use App\Traits\Common;
use App\Traits\FactorTrait;
use App\Traits\RequestProductWarehouseTrait;
use Illuminate\Support\Facades\Auth;

class FactorItemHelper
{
    use Common, RequestProductWarehouseTrait, FactorTrait;

    // attributes
    public iFactor $factor_interface;
    public iFactorItem $factor_item_interface;
    public iProductWarehouse $product_warehouse_interface;
    public iRequestProductWarehouse $request_product_interface;

    public function __construct(
        iFactor           $factor_interface,
        iFactorItem    $factor_item_interface,
        iProductWarehouse $product_warehouse_interface,
    )
    {
        $this->factor_interface = $factor_interface;
        $this->factor_item_interface = $factor_item_interface;
        $this->product_warehouse_interface = $product_warehouse_interface;
    }

    private function fillMorphFields($inputs)
    {
        switch ($inputs['item_type']) {
            case 'accessory':
                $inputs['model_type'] = Accessory::class;
                $inputs['model_id'] = $inputs['item_id'];
                break;
            case 'cloth':
                $inputs['model_type'] = Cloth::class;
                $inputs['model_id'] = Cloth::select('id')->whereCode($inputs['item_id'])->first()->id;
                break;
            case 'product':
                $inputs['model_type'] = Product::class;
                $inputs['model_id'] = Product::select('id')->whereCode($inputs['item_id'])->first()->id;
                break;
        }

        return $inputs;
    }


    /**
     * لیست کالاهای فاکتور
     * @param $inputs
     * @return array
     */
    public function getFactorItems($inputs): array
    {
        $inputs['order_by'] = $this->orderBy($inputs, 'factor_items');
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

        $select = ['id', 'factor_id', 'pack_count', 'metre', 'price', 'discount_type_id', 'discount'];
        $relation = [
            'model',
            'discount_type:enum_id,enum_caption'
        ];
        $factor_items = $this->factor_item_interface->getByFactorId($factor->id, $inputs, $select, $relation);

        $factor_items->transform(function ($item) {
            return [
                'id' => $item->id,
                'item' => [
                    'name' => $item->model->name
                ],
                'pack_count' => $item->pack_count,
                'metre' => $item->metre,
                'price' => $item->price,
                'discount_type' => [
                    'id' => $item->discount_type->enum_id,
                    'caption' => $item->discount_type->enum_caption
                ],
                'discount' => $item->discount,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $factor_items
        ];
    }

    /**
     * جزئیات کالای فاکتور
     * @param $inputs
     * @return array
     */
    public function getFactorItemDetail($inputs): array
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

        $select = ['id', 'factor_id', 'pack_count', 'metre', 'price', 'discount_type_id', 'discount'];
        $relation = [
            'model',
            'discount_type:enum_id,enum_caption'
        ];
        $factor_item = $this->factor_item_interface->getById($factor->id, $inputs['id'], $select, $relation);

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $factor_item
        ];
    }

    /**
     * افزودن کالای فاکتور
     * @param $inputs
     * @return array
     */
    public function addFactorItem($inputs): array
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

        $result = $this->CheckForChangeProduct($factor);
        if (!$result['result']) {
            return $result;
        }

        $inputs = $this->fillMorphFields($inputs);
        $res_factor = $this->factor_item_interface->addFactorItem($inputs, $factor->id, $user);
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
    public function deleteFactorItems($inputs): array
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

        $result = $this->CheckForChangeProduct($factor);
        if (!$result['result']) {
            return $result;
        }

        $result = (bool)$this->factor_item_interface->deleteFactorItems($factor->id);

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
    public function deleteFactorItem($inputs): array
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

        $result = $this->CheckForChangeProduct($factor);
        if (!$result['result']) {
            return $result;
        }

        $result = (bool) $this->factor_item_interface->deleteFactorItem($factor->id, $inputs['id']);

        return [
            'result' => $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
