<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\Facades\WarehouseItemFacade;
use App\Repositories\Interfaces\iPlace;
use App\Repositories\Interfaces\iProduct;
use App\Repositories\Interfaces\iProductModel;
use App\Repositories\Interfaces\iWarehouseItem;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductModelHelper
{
    use Common;

    // attributes
    public iPlace $place_interface;
    public iProduct $product_interface;
    public iProductModel $product_model_interface;
    public iWarehouseItem $warehouse_item_interface;

    public function __construct(
        iPlace $place_interface,
        iProduct $product_interface,
        iProductModel $product_model_interface,
        iWarehouseItem $warehouse_item_interface,
    )
    {
        $this->place_interface = $place_interface;
        $this->product_interface = $product_interface;
        $this->product_model_interface = $product_model_interface;
        $this->warehouse_item_interface = $warehouse_item_interface;
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
                'free_size_count' => $item->free_size_count,
                'size1_count' => $item->size1_count,
                'size2_count' => $item->size2_count,
                'size3_count' => $item->size3_count,
                'size4_count' => $item->size4_count,
                'pack_count' => $item->pack_count,
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

    public function getProductsModelCombo($inputs): array
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

    public function getProductModelCombo($inputs): array
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

    /**
     * @param $inputs
     * @return array
     * @throws ApiException
     */
    public function editProductModel($inputs): array
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

        $company_id = $this->getCurrentCompanyOfUser($user);
        $inputs['warehouse_id'] = $this->getCenterWarehouseOfCompany($company_id);
        $warehouse_item = WarehouseItemFacade::getWarehouseItemByData($inputs, $user)['data'];

        DB::beginTransaction();
        $result[] = $this->product_model_interface->editProductModel($product_model, $inputs);

        $inputs['sign'] = 'equal';
        $result[] = $this->warehouse_item_interface->editWarehouseItem($warehouse_item, $inputs, $user);

        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    public function addProductModel($inputs): array
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

        // تامین کننده مدل کالا
        $place = $this->place_interface->getPlaceById($inputs['place_id']);
        if (!$place) {
            return [
                'result' => 'false',
                'message' => __('messages.supplier_not_found'),
                'data' => null
            ];
        }

        $inputs['product_id'] = $product->id;
        $company_id = $this->getCurrentCompanyOfUser($user);
        $inputs['warehouse_id'] = $this->getCenterWarehouseOfCompany($company_id);
        $warehouse_item = WarehouseItemFacade::getWarehouseItemByData($inputs, $user)['data'];

        DB::beginTransaction();
        $res = $this->product_model_interface->addProductModel($inputs, $user);
        $result[] = $res['result'];

        $inputs['sign'] = 'plus';
        $result[] = $this->warehouse_item_interface->editWarehouseItem($warehouse_item, $inputs, $user);

        if (!in_array(false, $result)) {
            $data = $res['data'];
            $flag = true;
            DB::commit();
        } else {
            $data = null;
            $flag = false;
            DB::rollBack();
        }

        return [
            'result' => $flag,
            'message' => $flag ? __('messages.success') : __('messages.fail'),
            'data' => $data
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
