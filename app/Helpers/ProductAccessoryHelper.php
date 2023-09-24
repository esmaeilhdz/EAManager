<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iProduct;
use App\Repositories\Interfaces\iProductAccessory;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class ProductAccessoryHelper
{
    use Common;

    // attributes
    public iProduct $product_interface;
    public iProductAccessory $product_accessory_interface;

    public function __construct(
        iProduct $product_interface,
        iProductAccessory $product_accessory_interface
    )
    {
        $this->product_interface = $product_interface;
        $this->product_accessory_interface = $product_accessory_interface;
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

        $inputs['product_id'] = $product->id;
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $user = Auth::user();
        $product_accessories = $this->product_accessory_interface->getProductAccessories($inputs, $user);

        $product_accessories->transform(function ($item) {
            return [
                'id' => $item->id,
                'product_accessory' => $item->model->name,
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

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $product_accessory
        ];
    }

    public function getProductAccessoryCombo($inputs)
    {
        $user = Auth::user();
        $product_accessorys = $this->product_accessory_interface->getCombo($user);
        if (is_null($product_accessorys)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = null;
        foreach ($product_accessorys as $product_accessory) {
            $result[] = [
                'id' => $product_accessory->id,
                'name' => $product_accessory->product->name . ' - ' . $product_accessory->name
            ];
        }

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
     * سرویس جزئیات خرج کارکالا
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

        $result = $this->product_accessory_interface->deleteProductAccessory($product_accessory);

        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
