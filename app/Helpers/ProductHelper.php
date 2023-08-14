<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCloth;
use App\Repositories\Interfaces\iProduct;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class ProductHelper
{
    use Common;

    // attributes
    public iProduct $product_interface;
    public iCloth $cloth_interface;

    public function __construct(iProduct $product_interface, iCloth $cloth_interface)
    {
        $this->product_interface = $product_interface;
        $this->cloth_interface = $cloth_interface;
    }

    /**
     * سرویس لیست کالا ها
     * @param $inputs
     * @return array
     */
    public function getProducts($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'string:name');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $products = $this->product_interface->getProducts($inputs);

        $products->transform(function ($item) {
            $warehouse_count = 0;
            if (!is_null($item->productWarehouse)) {
                $warehouse_count = $item->productWarehouse->free_size_count + $item->productWarehouse->size1_count + $item->productWarehouse->size2_count + $item->productWarehouse->size3_count + $item->productWarehouse->size4_count;
            }
            return [
                'code' => $item->code,
                'name' => $item->name,
                'internal_code' => $item->internal_code,
                'has_accessories' => $item->has_accessories,
                'product_warehouse_count' => $warehouse_count,
                'price' => $item->productPrice->final_price ?? null,
                'cloth' => [
                    'name' => $item->cloth->name,
                    'color' => [
                        $item->cloth->color->caption
                    ]
                ],
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
            'data' => $products
        ];
    }

    /**
     * سرویس جزئیات کالا
     * @param $code
     * @return array
     */
    public function getProductDetail($code): array
    {
        $select = [
            'code', 'internal_code', 'name', 'has_accessories', 'cloth_id',
        ];
        $product = $this->product_interface->getProductByCode($code, $select);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $product
        ];
    }

    /**
     * سرویس ویرایش کالا
     * @param $inputs
     * @return array
     */
    public function editProduct($inputs): array
    {
        $product = $this->product_interface->getProductByCode($inputs['code']);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $cloth = $this->cloth_interface->getClothByCode($inputs['cloth_code']);
        if (is_null($cloth)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['cloth_id'] = $cloth->id;
        $result = $this->product_interface->editProduct($product, $inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * سرویس افزودن کالا
     * @param $inputs
     * @return array
     */
    public function addProduct($inputs): array
    {
        $user = Auth::user();

        $cloth = $this->cloth_interface->getClothByCode($inputs['cloth_code']);
        if (is_null($cloth)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['cloth_id'] = $cloth->id;
        $result = $this->product_interface->addProduct($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * سرویس حذف کالا
     * @param $inputs
     * @return array
     */
    public function deleteProduct($inputs): array
    {
        $product = $this->product_interface->getProductByCode($inputs['code'], ['id', 'code']);
        if (is_null($product)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->product_interface->deleteProduct($product);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }


}
