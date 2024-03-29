<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iProduct;
use App\Repositories\Interfaces\iProductWarehouse;
use App\Repositories\Interfaces\iSewing;
use App\Traits\Common;
use App\Traits\SewingTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SewingHelper
{
    use Common, SewingTrait;

    // attributes
    public iSewing $sewing_interface;
    public iProduct $product_interface;
    public iProductWarehouse $product_warehouse_interface;

    public function __construct(
        iSewing $sewing_interface,
        iProduct $product_interface,
        iProductWarehouse $product_warehouse_interface
    )
    {
        $this->sewing_interface = $sewing_interface;
        $this->product_interface = $product_interface;
        $this->product_warehouse_interface = $product_warehouse_interface;
    }

    /**
     * سرویس لیست دوخت ها
     * @param $inputs
     * @return array
     */
    public function getSewings($inputs): array
    {
        $user = Auth::user();
        // کالا
        $select = ['id'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name;family;national_code;concat_ws(" ",name,family);replace(concat_ws("",name,family)," ","")');
        $inputs['where']['person']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['person']['params'] = $param_array;

        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name');
        $inputs['where']['place']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['place']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'sewings');
        $inputs['per_page'] = $this->calculatePerPage($inputs);
        $inputs['product_id'] = $product->id;

        $sewings = $this->sewing_interface->getSewings($inputs);

        $sewings->transform(function ($item) {
            return [
                'id' => $item->id,
                'seamstress' => is_null($item->seamstress_person_id) ? null : $item->seamstress->name . ' ' . $item->seamstress->family,
                'place' => is_null($item->place_id) ? null : $item->place->name,
                'is_mozdi_dooz' => $item->is_mozdi_dooz,
                'count' => $item->count,
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
            'data' => $sewings
        ];
    }

    /**
     * سرویس جزئیات دوخت
     * @param $inputs
     * @return array
     */
    public function getSewingDetail($inputs): array
    {
        $user = Auth::user();
        // کالا
        $select = ['id'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['product_id'] = $product->id;
        $select = [
            'id', 'product_id', 'seamstress_person_id', 'place_id', 'is_mozdi_dooz', 'count', 'description'
        ];
        $relation = [
            'place:id,name',
            'seamstress:id,name,family',
            'accessories:accessory_id,sewing_id,accessory_count',
            'accessories.accessory:id,name',
        ];
        $sewing = $this->sewing_interface->getSewingById($inputs, $select, $relation);
        if (is_null($sewing)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $sewing
        ];
    }

    /**
     * سرویس ویرایش دوخت
     * @param $inputs
     * @return array
     */
    public function editSewing($inputs): array
    {
        $user = Auth::user();
        // کالا
        $select = ['id'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // دوخت
        $inputs['product_id'] = $product->id;
        $select = [
            'id', 'product_id', 'seamstress_person_id', 'place_id', 'is_mozdi_dooz', 'count', 'description'
        ];
        $sewing = $this->sewing_interface->getSewingById($inputs, $select);
        if (is_null($sewing)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // محاسبه تعداد دوخت برای ویرایش تعداد کالاهای انبار
        $params = $this->calculateForProductWarehouse($sewing, $inputs);

        // انبار
        $product_warehouse = $this->product_warehouse_interface->getByProductId($product->id);
        DB::beginTransaction();
        $result[] = $this->sewing_interface->editSewing($sewing, $inputs);

        if (!is_null($product_warehouse)) {
            $result[] = $this->product_warehouse_interface->editProductWarehouse($product_warehouse, $params);
        } else {
            $params = array_merge($inputs, $params);
            $result[] = $this->product_warehouse_interface->addProductWarehouse($params, $user);
        }

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
     * سرویس افزودن دوخت
     * @param $inputs
     * @return array
     */
    public function addSewing($inputs): array
    {
        $user = Auth::user();
        // کالا
        $select = ['id'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['product_id'] = $product->id;
        $result = $this->sewing_interface->addSewing($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * سرویس حذف دوخت
     * @param $inputs
     * @return array
     */
    public function deleteSewing($inputs): array
    {
        $user = Auth::user();
        // کالا
        $select = ['id'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['product_id'] = $product->id;
        $select = ['id', 'product_id', 'color_id', 'count'];
        $sewing = $this->sewing_interface->getSewingById($inputs, $select);
        if (is_null($sewing)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->sewing_interface->deleteSewing($sewing);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }


}
