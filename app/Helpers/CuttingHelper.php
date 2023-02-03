<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCutting;
use App\Repositories\Interfaces\iProduct;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CuttingHelper
{
    use Common;

    // attributes
    public iCutting $cutting_interface;
    public iProduct $product_interface;

    public function __construct(
        iCutting $cutting_interface,
        iProduct $product_interface
    )
    {
        $this->cutting_interface = $cutting_interface;
        $this->product_interface = $product_interface;
    }

    /**
     * سرویس لیست برش ها
     * @param $inputs
     * @return array
     */
    public function getCuttings($inputs): array
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

        $inputs['order_by'] = $this->orderBy($inputs, 'cuttings');
        $inputs['per_page'] = $this->calculatePerPage($inputs);
        $inputs['product_id'] = $product->id;

        $cuttings = $this->cutting_interface->getCuttings($inputs);

        $cuttings->transform(function ($item) {
            return [
                'id' => $item->id,
                'cutted_count' => $item->cutted_count,
                'free_size_count' => $item->free_size_count,
                'size1_count' => $item->size1_count,
                'size2_count' => $item->size2_count,
                'size3_count' => $item->size3_count,
                'size4_count' => $item->size4_count,
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
            'data' => $cuttings,
            'other' => [
                'product_name' => $product->name
            ]
        ];
    }

    /**
     * سرویس جزئیات برش
     * @param $inputs
     * @return array
     */
    public function getCuttingDetail($inputs): array
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

        $inputs['product_id'] = $product->id;
        $select = [
            'id', 'cutted_count', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count'
        ];
        $cutting = $this->cutting_interface->getCuttingById($inputs, $select);
        if (is_null($cutting)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $cutting
        ];
    }

    /**
     * سرویس ویرایش برش
     * @param $inputs
     * @return array
     */
    public function editCutting($inputs): array
    {
        // کالا
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
        $select = [
            'id', 'cutted_count', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count'
        ];
        $cutting = $this->cutting_interface->getCuttingById($inputs, $select);
        if (is_null($cutting)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->cutting_interface->editCutting($cutting, $inputs);

        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * سرویس افزودن برش
     * @param $inputs
     * @return array
     */
    public function addCutting($inputs): array
    {
        // کالا
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
        $user = Auth::user();
        $result = $this->cutting_interface->addCutting($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * سرویس حذف برش
     * @param $inputs
     * @return array
     */
    public function deleteCutting($inputs): array
    {
        // کالا
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
        $cutting = $this->cutting_interface->getCuttingById($inputs, $select);
        if (is_null($cutting)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->cutting_interface->deleteCutting($cutting);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }


}
