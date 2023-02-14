<?php

namespace App\Traits;

trait ProductToStoreTrait
{

    public function calculateForProductWarehouse($inputs, $product_warehouse, $product_to_store)
    {
        if ($inputs['free_size_count'] > $product_to_store->free_size_count) {
            $params['free_size_count'] = $product_warehouse->free_size_count - ($inputs['free_size_count'] - $product_to_store->free_size_count);
            if ($params['free_size_count'] < 0) {
                return [
                    'result' => false,
                    'message' => __('messages.free_size_not_enough'),
                    'data' => null
                ];
            }
        } elseif ($inputs['free_size_count'] < $product_to_store->free_size_count) {
            $params['free_size_count'] = $product_warehouse->free_size_count + ($product_to_store->free_size_count - $inputs['free_size_count']);
        } else {
            $params['free_size_count'] = $product_warehouse->free_size_count;
        }

        if ($inputs['size1_count'] > $product_to_store->size1_count) {
            $params['size1_count'] = $product_warehouse->size1_count - ($inputs['size1_count'] - $product_to_store->size1_count);
            if ($params['size1_count'] < 0) {
                return [
                    'result' => false,
                    'message' => __('messages.size1_not_enough'),
                    'data' => null
                ];
            }
        } elseif ($inputs['size1_count'] < $product_to_store->size1_count) {
            $params['size1_count'] = $product_warehouse->size1_count + ($product_to_store->size1_count - $inputs['size1_count']);
        } else {
            $params['size1_count'] = $product_warehouse->size1_count;
        }

        if ($inputs['size2_count'] > $product_to_store->size2_count) {
            $params['size2_count'] = $product_warehouse->size2_count - ($inputs['size2_count'] - $product_to_store->size2_count);
            if ($params['size2_count'] < 0) {
                return [
                    'result' => false,
                    'message' => __('messages.size2_not_enough'),
                    'data' => null
                ];
            }
        } elseif ($inputs['size2_count'] < $product_to_store->size2_count) {
            $params['size2_count'] = $product_warehouse->size2_count + ($product_to_store->size2_count - $inputs['size2_count']);
        } else {
            $params['size2_count'] = $product_warehouse->size2_count;
        }

        if ($inputs['size3_count'] > $product_to_store->size3_count) {
            $params['size3_count'] = $product_warehouse->size3_count - ($inputs['size3_count'] - $product_to_store->size3_count);
            if ($params['size3_count'] < 0) {
                return [
                    'result' => false,
                    'message' => __('messages.size3_not_enough'),
                    'data' => null
                ];
            }
        } elseif ($inputs['size3_count'] < $product_to_store->size3_count) {
            $params['size3_count'] = $product_warehouse->size3_count + ($product_to_store->size3_count - $inputs['size3_count']);
        } else {
            $params['size3_count'] = $product_warehouse->size3_count;
        }

        if ($inputs['size4_count'] > $product_to_store->size4_count) {
            $params['size4_count'] = $product_warehouse->size4_count - ($inputs['size4_count'] - $product_to_store->size4_count);
            if ($params['size4_count'] < 0) {
                return [
                    'result' => false,
                    'message' => __('messages.size4_not_enough'),
                    'data' => null
                ];
            }
        } elseif ($inputs['size4_count'] < $product_to_store->size4_count) {
            $params['size4_count'] = $product_warehouse->size4_count + ($product_to_store->size4_count - $inputs['size4_count']);
        } else {
            $params['size4_count'] = $product_warehouse->size4_count;
        }

        return [
            'result' => true,
            'message' => null,
            'data' => $params
        ];
    }
}
