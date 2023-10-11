<?php

namespace App\Traits;

trait FactorTrait
{
    // فاکتور ناقص
    static int $inCompleteFactor = 1;
    // فاکتور تایید شده
    static int $confirmFactor = 2;
    // فاکتور مرجوعی
    static int $returnedFactor = 3;


    public function changeStatusFactorHelper($status, $factor): array
    {
        $params = null;
        $select = ['product_warehouse_id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count'];
        $relation = [
            'product_warehouse:id,free_size_count,size1_count,size2_count,size3_count,size4_count'
        ];
        $factor_products = $this->factor_product_interface->getByFactorId($factor->id, [], $select, $relation);
        // تکمیل فاکتور ناقص
        // باید موجودی انبار موردنظر به تعداد خریداری شده، کسر شود.
        if ($status == 2) {
            foreach ($factor_products as $key => $factor_product) {
                $params[$key]['sign'] = 'minus';
                $params[$key]['free_size_count'] = $factor_product->free_size_count;
                $params[$key]['size1_count'] = $factor_product->size1_count;
                $params[$key]['size2_count'] = $factor_product->size2_count;
                $params[$key]['size3_count'] = $factor_product->size3_count;
                $params[$key]['size4_count'] = $factor_product->size4_count;
            }
        } elseif ($status == 3) {
            // مرجوع فاکتور
            // باید موجودی انبار موردنظر به تعداد خریداری شده، اضافه شود.
            // اجازه مرجوع ندارد
            if ($factor->has_return_permission == 0) {
                return [
                    'result' => false,
                    'message' => __('messages.factor_doesnt_has_return_permission'),
                    'data' => null
                ];
            }

            foreach ($factor_products as $key => $factor_product) {
                $params[$key]['sign'] = 'plus';
                $params[$key]['free_size_count'] = $factor_product->free_size_count;
                $params[$key]['size1_count'] = $factor_product->size1_count;
                $params[$key]['size2_count'] = $factor_product->size2_count;
                $params[$key]['size3_count'] = $factor_product->size3_count;
                $params[$key]['size4_count'] = $factor_product->size4_count;
            }

        }

        return [
            'result' => true,
            'message' => null,
            'data' => [
                'params' => $params,
                'factor_products' => $factor_products
            ]
        ];
    }

    public function prepareWarehouseToAddCompleteFactor($inputs, $product_warehouse, $product_item): array
    {
        $inputs['free_size_count'] = $product_warehouse->free_size_count - $product_item['free_size_count'];
        $inputs['size1_count'] = $product_warehouse->size1_count - $product_item['size1_count'];
        $inputs['size2_count'] = $product_warehouse->size2_count - $product_item['size2_count'];
        $inputs['size3_count'] = $product_warehouse->size3_count - $product_item['size3_count'];
        $inputs['size4_count'] = $product_warehouse->size4_count - $product_item['size4_count'];

        return $inputs;
    }

    /**
     * بررسی برای تغییر کالا
     * @param $factor
     * @return array
     */
    public function CheckForChangeProduct($factor): array
    {
        $result = [
            'result' => true,
            'message' => null,
            'data' => null
        ];

        if ($factor->status == self::$confirmFactor) {
            $result = [
                'result' => false,
                'message' => __('messages.factor_already_confirmed_cannot_do_operation'),
                'data' => null
            ];
        }

        if ($factor->status == self::$returnedFactor) {
            $result = [
                'result' => false,
                'message' => __('messages.factor_already_returned_cannot_do_operation'),
                'data' => null
            ];
        }

        return $result;
    }

    /**
     * بررسی برای تغییر پرداخت
     * @param $factor
     * @return array
     */
    public function CheckForChangePayment($factor): array
    {
        $result = [
            'result' => true,
            'message' => null,
            'data' => null
        ];

        if ($factor->status == self::$confirmFactor) {
            $result = [
                'result' => false,
                'message' => __('messages.factor_already_confirmed_cannot_do_operation'),
                'data' => null
            ];
        }

        if ($factor->status == self::$returnedFactor) {
            $result = [
                'result' => false,
                'message' => __('messages.factor_already_returned_cannot_do_operation'),
                'data' => null
            ];
        }

        return $result;
    }
}
