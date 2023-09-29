<?php

namespace App\Service\Factor;

class CheckFactor
{

    // فاکتور ناقص
    const InCompleteFactor = 1;
    // فاکتور تایید شده
    const ConfirmFactor = 2;
    // فاکتور مرجوعی
    const ReturnedFactor = 3;


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

        if ($factor->status == self::ConfirmFactor) {
            $result = [
                'result' => false,
                'message' => __('messages.factor_already_confirmed_cannot_do_operation'),
                'data' => null
            ];
        }

        if ($factor->status == self::ReturnedFactor) {
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

        if ($factor->status == self::ConfirmFactor) {
            $result = [
                'result' => false,
                'message' => __('messages.factor_already_confirmed_cannot_do_operation'),
                'data' => null
            ];
        }

        if ($factor->status == self::ReturnedFactor) {
            $result = [
                'result' => false,
                'message' => __('messages.factor_already_returned_cannot_do_operation'),
                'data' => null
            ];
        }

        return $result;
    }
}
