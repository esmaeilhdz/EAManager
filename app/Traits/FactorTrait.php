<?php

namespace App\Traits;

trait FactorTrait
{
    public function changeStatusFactorHelper($status, $factor): array
    {
        $params = null;
        $select = ['product_warehouse_id', 'free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count'];
        $relation = [
            'product_warehouse:id,free_size_count,size1_count,size2_count,size3_count,size4_count'
        ];
        $factor_products = $this->factor_product_interface->getByFactorId($factor->id, $select, $relation);
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
}
