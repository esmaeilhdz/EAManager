<?php

namespace App\Traits;

trait RequestProductWarehouseTrait
{

    /**
     * محاسبه برای درخواست اتومات از انبار
     * @param $product_warehouse
     * @param $result_check_stock
     * @param $inputs
     * @return array
     */
    public function calculateForRequest($product_warehouse, $result_check_stock, $inputs): array
    {
        $params = null;
        $params['warehouse_id'] = $product_warehouse->id;
        $params['free_size_count'] = 0;
        $params['size1_count'] = 0;
        $params['size2_count'] = 0;
        $params['size3_count'] = 0;
        $params['size4_count'] = 0;
        if (in_array('free_size_count', $result_check_stock['size'])) {
            $params['free_size_count'] = $product_warehouse->free_size_count - $inputs['free_size_count'];
        }
        if (in_array('size1_count', $result_check_stock['size'])) {
            $params['size1_count'] = $product_warehouse->size1_count - $inputs['size1_count'];
        }
        if (in_array('size2_count', $result_check_stock['size'])) {
            $params['size2_count'] = $product_warehouse->size2_count - $inputs['size2_count'];
        }
        if (in_array('size3_count', $result_check_stock['size'])) {
            $params['size3_count'] = $product_warehouse->size3_count - $inputs['size3_count'];
        }
        if (in_array('size4_count', $result_check_stock['size'])) {
            $params['size4_count'] = $product_warehouse->size4_count - $inputs['size4_count'];
        }

        return $params;
    }

    public function calculateForProductWarehouse($product_warehouse, $request_product_warehouse): array
    {
        $flag = true;
        $message = null;
        $data = null;
        if ($product_warehouse->free_size_count - $request_product_warehouse->free_size_count < 0) {
            $flag = false;
            $message = __('messages.free_size_not_enough');
        } elseif ($product_warehouse->size1_count - $request_product_warehouse->size1_count < 0) {
            $flag = false;
            $message = __('messages.size1_not_enough');
        } elseif ($product_warehouse->size2_count - $request_product_warehouse->size2_count < 0) {
            $flag = false;
            $message = __('messages.size2_not_enough');
        } elseif ($product_warehouse->size3_count - $request_product_warehouse->size3_count < 0) {
            $flag = false;
            $message = __('messages.size3_not_enough');
        } elseif ($product_warehouse->size4_count - $request_product_warehouse->size4_count < 0) {
            $flag = false;
            $message = __('messages.size4_not_enough');
        } else {
            $inputs_product_warehouse = [
                'free_size_count' => $product_warehouse->free_size_count,
                'size1_count' => $product_warehouse->size1_count,
                'size2_count' => $product_warehouse->size2_count,
                'size3_count' => $product_warehouse->size3_count,
                'size4_count' => $product_warehouse->size4_count,
            ];

            if ($request_product_warehouse->free_size_count > 0) {
                $inputs_product_warehouse['free_size_count'] = $product_warehouse->free_size_count - $request_product_warehouse->free_size_count;
            }
            if ($request_product_warehouse->size1_count > 0) {
                $inputs_product_warehouse['size1_count'] = $product_warehouse->size1_count - $request_product_warehouse->size1_count;
            }
            if ($request_product_warehouse->size2_count > 0) {
                $inputs_product_warehouse['size2_count'] = $product_warehouse->size2_count - $request_product_warehouse->size2_count;
            }
            if ($request_product_warehouse->size3_count > 0) {
                $inputs_product_warehouse['size3_count'] = $product_warehouse->size3_count - $request_product_warehouse->size3_count;
            }
            if ($request_product_warehouse->size4_count > 0) {
                $inputs_product_warehouse['size4_count'] = $product_warehouse->size4_count - $request_product_warehouse->size4_count;
            }

            $inputs_request_product_warehouse = [
                'sign' => 'plus',
                'free_size_count' => $request_product_warehouse->free_size_count,
                'size1_count' => $request_product_warehouse->size1_count,
                'size2_count' => $request_product_warehouse->size2_count,
                'size3_count' => $request_product_warehouse->size3_count,
                'size4_count' => $request_product_warehouse->size4_count,
            ];

            $data = [
                'inputs_product_warehouse' => $inputs_product_warehouse,
                'inputs_request_product_warehouse' => $inputs_request_product_warehouse,
            ];
        }

        return [
            'result' => $flag,
            'message' => $message,
            'data' => $data
        ];
    }
}
