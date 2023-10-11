<?php

namespace App\Helpers;

use App\Models\Accessory;
use App\Models\Cloth;
use App\Repositories\Interfaces\iAccessory;
use App\Repositories\Interfaces\iCloth;
use App\Repositories\Interfaces\iProduct;
use App\Repositories\Interfaces\iProductAccessory;
use App\Repositories\Interfaces\iProductAccessoryPrice;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductAccessoryHelper
{
    use Common;

    // attributes
    public iCloth $cloth_interface;
    public iProduct $product_interface;
    public iAccessory $accessory_interface;
    public iProductAccessory $product_accessory_interface;
    public iProductAccessoryPrice $product_accessory_price_interface;

    public function __construct(
        iCloth $cloth_interface,
        iProduct $product_interface,
        iAccessory $accessory_interface,
        iProductAccessory $product_accessory_interface,
        iProductAccessoryPrice $product_accessory_price_interface,
    )
    {
        $this->cloth_interface = $cloth_interface;
        $this->product_interface = $product_interface;
        $this->accessory_interface = $accessory_interface;
        $this->product_accessory_interface = $product_accessory_interface;
        $this->product_accessory_price_interface = $product_accessory_price_interface;
    }

    /**
     * سرویس لیست خرج کارهای کالا
     * @param $inputs
     * @return array
     */
    public function getProductAccessories($inputs): array
    {
        $user = Auth::user();
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, ['id']);
        if (!$product) {
            return [
                'result' => 'false',
                'message' => __('messages.product_not_found'),
                'data' => null
            ];
        }

        $user = Auth::user();
        $inputs['product_id'] = $product->id;
        $product_accessories = $this->product_accessory_interface->getProductAccessories($inputs, $user);

        $product_accessories->transform(function ($item) {
            $type = $item->model_type == Cloth::class ? 'cloth' : 'accessory';
            $product_accessory = [
                'type' => $type,
                'name' => $item->model->name
            ];

            if ($type == 'cloth') {
                $product_accessory['code'] = $item->model->code;
            } else {
                $product_accessory['id'] = $item->model_id;
            }

            return [
                'id' => $item->id,
                'product_accessory' => $product_accessory,
                'amount' => $item->amount,
                'created_at' => $item->created_at,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $product_accessories
        ];
    }

    /**
     * سرویس جزئیات خرج کارکالا
     * @param $inputs
     * @return array
     */
    public function getProductAccessoryDetail($inputs): array
    {
        $user = Auth::user();
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, ['id']);
        if (!$product) {
            return [
                'result' => 'false',
                'message' => __('messages.product_not_found'),
                'data' => null
            ];
        }

        $inputs['product_id'] = $product->id;
        $user = Auth::user();
        $product_accessory = $this->product_accessory_interface->getById($inputs['product_id'], $inputs['id'], $user);
        if (is_null($product_accessory)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $type = $product_accessory->model_type == Cloth::class ? 'cloth' : 'accessory';
        $product_accessory_array = [
            'type' => $type,
            'name' => $product_accessory->model->name
        ];

        if ($type == 'cloth') {
            $product_accessory_array['code'] = $product_accessory->model->code;
        } else {
            $product_accessory_array['id'] = $product_accessory->model_id;
        }

        $result = [
            'id' => $product_accessory->id,
            'product_accessory' => $product_accessory_array,
            'amount' => $product_accessory->amount,
        ];

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $result
        ];
    }

    public function editProductAccessory($inputs)
    {
        $user = Auth::user();
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, ['id']);
        if (!$product) {
            return [
                'result' => 'false',
                'message' => __('messages.product_not_found'),
                'data' => null
            ];
        }

        $inputs['product_id'] = $product->id;
        $user = Auth::user();
        $product_accessory = $this->product_accessory_interface->getById($inputs['product_id'], $inputs['id'], $user);
        if (!$product_accessory) {
            return [
                'result' => 'false',
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        if (!is_null($inputs['cloth_code'])) {
            $cloth = $this->cloth_interface->getClothByCode($inputs['cloth_code'], $user);
            if (!$cloth) {
                return [
                    'result' => 'false',
                    'message' => __('messages.cloth_not_found'),
                    'data' => null
                ];
            }
            $inputs['model_type'] = Cloth::class;
            $inputs['model_id'] = $cloth->id;
        } elseif (!is_null($inputs['accessory_id'])) {
            $accessory = $this->accessory_interface->getAccessoryById($inputs['accessory_id'], ['id']);
            if (!$accessory) {
                return [
                    'result' => 'false',
                    'message' => __('messages.accessory_not_found'),
                    'data' => null
                ];
            }
            $inputs['model_type'] = Accessory::class;
            $inputs['model_id'] = $accessory->id;
        }

        $result = $this->product_accessory_interface->editProductAccessory($product_accessory, $inputs);

        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    public function addProductAccessory($inputs)
    {
        $user = Auth::user();
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, ['id']);
        if (!$product) {
            return [
                'result' => 'false',
                'message' => __('messages.product_not_found'),
                'data' => null
            ];
        }

        if (!is_null($inputs['cloth_code'])) {
            $cloth = $this->cloth_interface->getClothByCode($inputs['cloth_code'], $user);
            if (!$cloth) {
                return [
                    'result' => 'false',
                    'message' => __('messages.cloth_not_found'),
                    'data' => null
                ];
            }
            $inputs['model_type'] = Cloth::class;
            $inputs['model_id'] = $cloth->id;
        } elseif (!is_null($inputs['accessory_id'])) {
            $accessory = $this->accessory_interface->getAccessoryById($inputs['accessory_id'], ['id']);
            if (!$accessory) {
                return [
                    'result' => 'false',
                    'message' => __('messages.accessory_not_found'),
                    'data' => null
                ];
            }
            $inputs['model_type'] = Accessory::class;
            $inputs['model_id'] = $accessory->id;
        }

        $inputs['product_id'] = $product->id;
        $user = Auth::user();
        $result = $this->product_accessory_interface->addProductAccessory($inputs, $user);

        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * سرویس حذف خرج کارکالا
     * @param $inputs
     * @return array
     */
    public function deleteProductAccessory($inputs): array
    {
        $user = Auth::user();
        $product = $this->product_interface->getProductByCode($inputs['code'], $user, ['id']);
        if (!$product) {
            return [
                'result' => 'false',
                'message' => __('messages.product_not_found'),
                'data' => null
            ];
        }

        $inputs['product_id'] = $product->id;
        $user = Auth::user();
        $product_accessory = $this->product_accessory_interface->getById($inputs['product_id'], $inputs['id'], $user);
        if (!$product_accessory) {
            return [
                'result' => 'false',
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        DB::beginTransaction();
        $result[] = $this->product_accessory_price_interface->deleteByProductAccessoryId($product_accessory->id);
        $result[] = $this->product_accessory_interface->deleteProductAccessory($product_accessory);

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

}
