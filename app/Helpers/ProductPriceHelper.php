<?php

namespace App\Helpers;

use App\Models\Cloth;
use App\Repositories\Interfaces\iPerson;
use App\Repositories\Interfaces\iPlace;
use App\Repositories\Interfaces\iProduct;
use App\Repositories\Interfaces\iProductAccessory;
use App\Repositories\Interfaces\iProductAccessoryPrice;
use App\Repositories\Interfaces\iProductPrice;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductPriceHelper
{
    use Common;

    // attributes
    public iPlace $place_interface;
    public iPerson $person_interface;
    public iProduct $product_interface;
    public iProductPrice $product_price_interface;
    public iProductAccessoryPrice $product_accessory_price_interface;

    public function __construct(
        iPlace $place_interface,
        iPerson $person_interface,
        iProduct $product_interface,
        iProductPrice $product_price_interface,
        iProductAccessoryPrice $product_accessory_price_interface,
    )
    {
        $this->place_interface = $place_interface;
        $this->person_interface = $person_interface;
        $this->product_interface = $product_interface;
        $this->product_price_interface = $product_price_interface;
        $this->product_accessory_price_interface = $product_accessory_price_interface;
    }

    /**
     * سرویس لیست قیمت های کالا
     * @param $inputs
     * @return array
     */
    public function getProductPrices($inputs): array
    {
        $user = Auth::user();
        $select = ['id', 'code', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['order_by'] = $this->orderBy($inputs, 'product_prices');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $product_prices = $this->product_price_interface->getProductPrices($product->id, $inputs);

        $product_prices->transform(function ($item) {
            $product_accessory_prices = null;
            foreach ($item->product_accessory_price as $product_accessory_price) {
                $product_accessory_prices[] = [
                    'product_accessory_id' => $product_accessory_price->product_accessory_id,
                    'type' => $product_accessory_price->product_accessory->model_type == Cloth::class ? 'cloth' : 'accessory',
                    'name' => $product_accessory_price->product_accessory->model->name,
                    'price' => $product_accessory_price->price
                ];
            }

            if (is_null($item->cutter_person_id)) {
                $cutter = $item->cutter_place->name;
                $cutter_type = 'خارجی';
            } else {
                $cutter = $item->cutter_person->name . ' ' . $item->cutter_person->family;
                $cutter_type = 'داخلی';
            }
            return [
                'id' => $item->id,
                'cutter' => [
                    'name' => $cutter,
                    'type' => $cutter_type
                ],
                'total_count' => $item->total_count,
                'serial_count' => $item->serial_count,
                'sewing_date' => $item->sewing_date,
                'sewing_price' => $item->sewing_price,
                'cutting_price' => $item->cutting_price,
                'cutting_date' => $item->cutting_date,
                'packing_price' => $item->packing_price,
                'sending_price' => $item->sending_price,
                'sewing_final_price' => $item->sewing_final_price,
                'sale_profit_price' => $item->sale_profit_price,
                'final_price' => $item->final_price,
                'is_enable' => $item->is_enable,
                'product_accessory_prices' => $product_accessory_prices,
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
            'data' => $product_prices,
            'other' => [
                'product_name' => $product->name
            ]
        ];
    }

    /**
     * سرویس جزئیات قیمت کالا
     * @param $inputs
     * @return array
     */
    public function getProductPriceDetail($inputs): array
    {
        $user = Auth::user();
        // کالا
        $select = ['id', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // قیمت کالا
        $inputs['product_id'] = $product->id;
        $relation = ['product:id,name'];
        $select = ['product_id', 'total_count', 'serial_count', 'sewing_price', 'cutting_price', 'sewing_final_price', 'sale_profit_price', 'final_price', 'is_enable'];
        $product_price = $this->product_price_interface->getProductPriceById($inputs, $select, $relation);
        if (is_null($product_price)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $product_price
        ];
    }

    /**
     * سرویس ویرایش قیمت کالا
     * @param $inputs
     * @return array
     */
    public function editProductPrice($inputs): array
    {
        $user = Auth::user();
        // کالا
        $select = ['id', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['product_id'] = $product->id;
        $product_price = $this->product_price_interface->getProductPriceById($inputs);
        if (is_null($product_price)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['cutter_person_id'] = null;
        if (!is_null($inputs['cutter_person_code'])) {
            $cutter_person = $this->person_interface->getPersonByCode($inputs['cutter_person_code'], ['id']);
            if (!$cutter_person) {
                return [
                    'result' => false,
                    'message' => __('messages.cutter_not_found'),
                    'data' => null
                ];
            }
            $inputs['cutter_person_id'] = $cutter_person->id;
        }

        if (!is_null($inputs['cutter_place_id'])) {
            $cutter_place = $this->place_interface->getPlaceById($inputs['cutter_place_id']);
            if (!$cutter_place) {
                return [
                    'result' => false,
                    'message' => __('messages.cutter_not_found'),
                    'data' => null
                ];
            }
            $inputs['cutter_place_id'] = $cutter_place->id;
        }

        DB::beginTransaction();
        $result[] = $this->product_price_interface->deActiveOldPrices($product->id);
        $res = $this->product_price_interface->addProductPrice($inputs, $user);
        $result[] = $res['result'];
        $product_price_id = $res['data'];
        $result[] = $this->product_accessory_price_interface->deleteByProductPriceId($product_price->id);
        foreach ($inputs['product_accessories'] as $product_accessory) {
            $res = $this->product_accessory_price_interface->addProductAccessoryPrice($product_accessory, $product_price_id);
            $result[] = $res['result'];
        }

        $result = $this->prepareTransactionArray($result);

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
     * سرویس افزودن قیمت کالا
     * @param $inputs
     * @return array
     */
    public function addProductPrice($inputs): array
    {
        $user = Auth::user();
        // کالا
        $select = ['id', 'name'];
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        if (!is_null($inputs['cutter_person_code'])) {
            $cutter_person = $this->person_interface->getPersonByCode($inputs['cutter_person_code'], ['id']);
            if (!$cutter_person) {
                return [
                    'result' => false,
                    'message' => __('messages.cutter_not_found'),
                    'data' => null
                ];
            }
            $inputs['cutter_person_id'] = $cutter_person->id;
        }

        if (!is_null($inputs['cutter_place_id'])) {
            $cutter_place = $this->place_interface->getPlaceById($inputs['cutter_place_id']);
            if (!$cutter_place) {
                return [
                    'result' => false,
                    'message' => __('messages.cutter_not_found'),
                    'data' => null
                ];
            }
            $inputs['cutter_place_id'] = $cutter_place->id;
        }

        DB::beginTransaction();
        $inputs['product_id'] = $product->id;
        $result[] = $this->product_price_interface->deActiveOldPrices($product->id);
        $res = $this->product_price_interface->addProductPrice($inputs, $user);
        $result[] = $res['result'];
        foreach ($inputs['product_accessories'] as $product_accessory) {
            $result[] = $this->product_accessory_price_interface->addProductAccessoryPrice($product_accessory, $res['data']);
        }

        $result = $this->prepareTransactionArray($result);

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
     * سرویس حذف قیمت کالا
     * @param $inputs
     * @return array
     */
    public function deleteProductPrice($inputs): array
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

        // قیمت کالا
        $inputs['product_id'] = $product->id;
        $product_price = $this->product_price_interface->getProductPriceById($inputs, ['id']);
        if (is_null($product_price)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        DB::beginTransaction();
        $result[] = $this->product_accessory_price_interface->deleteByProductPriceId($product_price->id);
        $result[] = $this->product_price_interface->deleteProductPrice($product_price);

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
