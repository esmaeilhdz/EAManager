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
        $select = ['pack_count', 'metre', 'count'];
        $factor_items = $this->factor_item_interface->getByFactorId($factor->id, [], $select/*, $relation*/);
        // تکمیل فاکتور ناقص
        // باید موجودی انبار موردنظر به تعداد خریداری شده، کسر شود.
        if ($status == 2) {
            foreach ($factor_items as $key => $factor_item) {
                $params[$key]['sign'] = 'minus';
                $params[$key]['pack_count'] = $factor_item->pack_count;
                $params[$key]['metre'] = $factor_item->metre;
                $params[$key]['count'] = $factor_item->count;
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

            foreach ($factor_items as $key => $factor_item) {
                $params[$key]['sign'] = 'plus';
                $params[$key]['pack_count'] = $factor_item->pack_count;
                $params[$key]['metre'] = $factor_item->metre;
                $params[$key]['count'] = $factor_item->count;
            }

        }

        return [
            'result' => true,
            'message' => null,
            'data' => [
                'params' => $params,
                'factor_items' => $factor_items
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
