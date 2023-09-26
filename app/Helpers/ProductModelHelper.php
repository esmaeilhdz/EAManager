<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCloth;
use App\Repositories\Interfaces\iProduct;
use App\Repositories\Interfaces\iProductModel;
use App\Repositories\Interfaces\iSalePeriod;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class ProductModelHelper
{
    use Common;

    // attributes
    public iProduct $product_interface;
    public iProductModel $product_model_interface;

    public function __construct(
        iProduct $product_interface,
        iProductModel $product_model_interface
    )
    {
        $this->product_interface = $product_interface;
        $this->product_model_interface = $product_model_interface;
    }

    /**
     * سرویس لیست مدل های کالا
     * @param $inputs
     * @return array
     */
    public function getProductModels($inputs): array
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
        $product_models = $this->product_model_interface->getProductModels($inputs, $user);

        $product_models->transform(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'created_at' => $item->created_at,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $product_models
        ];
    }

    /**
     * سرویس جزئیات مدل کالا
     * @param $inputs
     * @return array
     */
    public function getProductModelDetail($inputs): array
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
        $product_model = $this->product_model_interface->getById($inputs['product_id'], $inputs['id'], $user);
        if (is_null($product_model)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $product_model
        ];
    }

    public function getProductsModelCombo($inputs)
    {
        $user = Auth::user();
        $product_models = $this->product_model_interface->getProductsModelCombo($inputs, $user);
        if (is_null($product_models)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = null;
        foreach ($product_models as $product_model) {
            $result[] = [
                'id' => $product_model->id,
                'name' => $product_model->product->name . ' - ' . $product_model->name
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $result
        ];
    }

    public function getProductModelCombo($inputs)
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
        $product_models = $this->product_model_interface->getProductModelCombo($inputs, $user);
        if (is_null($product_models)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $product_models
        ];
    }

    public function editProductModel($inputs)
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
        $product_model = $this->product_model_interface->getById($inputs['product_id'], $inputs['id'], $user);
        if (!$product_model) {
            return [
                'result' => 'false',
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->product_model_interface->editProductModel($product_model, $inputs);

        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    public function addProductModel($inputs)
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
        $result = $this->product_model_interface->addProductModel($inputs, $user);

        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * سرویس جزئیات مدل کالا
     * @param $inputs
     * @return array
     */
    public function deleteProductModel($inputs): array
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
        $product_model = $this->product_model_interface->getById($inputs['product_id'], $inputs['id'], $user);
        if (!$product_model) {
            return [
                'result' => 'false',
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->product_model_interface->deleteProductModel($product_model);

        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
