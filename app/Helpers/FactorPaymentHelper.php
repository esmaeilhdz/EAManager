<?php

namespace App\Helpers;

use App\Repositories\Interfaces\iCustomer;
use App\Repositories\Interfaces\iFactor;
use App\Repositories\Interfaces\iFactorPayment;
use App\Repositories\Interfaces\iRequestProductWarehouse;
use App\Traits\Common;
use App\Traits\FactorTrait;
use App\Traits\RequestProductWarehouseTrait;
use Illuminate\Support\Facades\Auth;

class FactorPaymentHelper
{
    use Common, RequestProductWarehouseTrait, FactorTrait;

    // فاکتور ناقص
    const InCompleteFactor = 1;
    // فاکتور تایید شده
    const ConfirmFactor = 2;
    // فاکتور مرجوعی
    const ReturnedFactor = 3;

    // attributes
    public iFactor $factor_interface;
    public iCustomer $customer_interface;
    public iFactorPayment $factor_payment_interface;
    public iRequestProductWarehouse $request_payment_interface;

    public function __construct(
        iFactor                  $factor_interface,
        iCustomer                $customer_interface,
        iFactorPayment           $factor_payment_interface,
        iRequestProductWarehouse $request_payment_interface,
    )
    {
        $this->factor_interface = $factor_interface;
        $this->customer_interface = $customer_interface;
        $this->factor_payment_interface = $factor_payment_interface;
        $this->request_payment_interface = $request_payment_interface;
    }

    /**
     * لیست پرداخت های فاکتور
     * @param $inputs
     * @return array
     */
    public function getFactorPayments($inputs): array
    {
        $inputs['order_by'] = $this->orderBy($inputs, 'factor_payments');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $user = Auth::user();
        $factor = $this->factor_interface->getFactorByCode($inputs['code'], $user);
        if (is_null($factor)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $select = ['id', 'factor_id', 'payment_type_id', 'price', 'description'];
        $relation = [
            'payment_type:enum_id,enum_caption'
        ];
        $factor_payments = $this->factor_payment_interface->getByFactorId($factor->id, $select, $relation);

        $factor_payments->transform(function ($item) {
            return [
                'id' => $item->id,
                'payment_type' => [
                    'id' => $item->payment_type_id,
                    'caption' => $item->payment_type->enum_caption
                ],
                'price' => $item->price,
            ];
        });

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $factor_payments
        ];
    }

    /**
     * جزئیات پرداخت فاکتور
     * @param $inputs
     * @return array
     */
    public function getFactorPaymentDetail($inputs): array
    {
        $user = Auth::user();
        $factor = $this->factor_interface->getFactorByCode($inputs['code'], $user);
        if (is_null($factor)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $select = ['id', 'factor_id', 'payment_type_id', 'price', 'description'];
        $relation = [
            'payment_type:enum_id,enum_caption',
        ];
        $factor_payment = $this->factor_payment_interface->getById($factor->id, $inputs['id'], $select, $relation);

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $factor_payment
        ];
    }

    /**
     * افزودن پرداخت فاکتور
     * @param $inputs
     * @return array
     */
    public function addFactorPayment($inputs): array
    {
        $user = Auth::user();
        $factor = $this->factor_interface->getFactorByCode($inputs['code'], $user);
        if (is_null($factor)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // برای افزودن پرداخت به فاکتور، فاکتور نباید تایید نهایی شده باشد.
        if ($factor->status == self::ConfirmFactor) {
            return [
                'result' => false,
                'message' => __('messages.factor_already_confirmed_cannot_add_payment'),
                'data' => null
            ];
        }

        // برای افزودن پرداخت  به فاکتور، فاکتور نباید مرجوع شده باشد.
        if ($factor->status == self::ReturnedFactor) {
            return [
                'result' => false,
                'message' => __('messages.factor_already_returned_cannot_add_payment'),
                'data' => null
            ];
        }

        $res_factor = $this->factor_payment_interface->addFactorPayment($inputs, $factor->id, $user);
        $result = $res_factor['result'];

        return [
            'result' => $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => $result ? $res_factor['data'] : null
        ];
    }

    /**
     * حذف پرداخت های فاکتور
     * @param $inputs
     * @return array
     */
    public function deleteFactorPayments($inputs): array
    {
        $user = Auth::user();
        $factor = $this->factor_interface->getFactorByCode($inputs['code'], $user);
        if (is_null($factor)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // برای افزودن پرداخت به فاکتور، فاکتور نباید تایید نهایی شده باشد.
        if ($factor->status == self::ConfirmFactor) {
            return [
                'result' => false,
                'message' => __('messages.factor_already_confirmed_cannot_add_payment'),
                'data' => null
            ];
        }

        // برای افزودن پرداخت به فاکتور، فاکتور نباید مرجوع شده باشد.
        if ($factor->status == self::ReturnedFactor) {
            return [
                'result' => false,
                'message' => __('messages.factor_already_returned_cannot_add_payment'),
                'data' => null
            ];
        }

        $result = (bool)$this->factor_payment_interface->deleteFactorPayments($factor->id);

        return [
            'result' => $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * حذف پرداخت فاکتور
     * @param $inputs
     * @return array
     */
    public function deleteFactorPayment($inputs): array
    {
        $user = Auth::user();
        $factor = $this->factor_interface->getFactorByCode($inputs['code'], $user);
        if (is_null($factor)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        // برای افزودن پرداخت به فاکتور، فاکتور نباید تایید نهایی شده باشد.
        if ($factor->status == self::ConfirmFactor) {
            return [
                'result' => false,
                'message' => __('messages.factor_already_confirmed_cannot_add_payment'),
                'data' => null
            ];
        }

        // برای افزودن پرداخت به فاکتور، فاکتور نباید مرجوع شده باشد.
        if ($factor->status == self::ReturnedFactor) {
            return [
                'result' => false,
                'message' => __('messages.factor_already_returned_cannot_add_payment'),
                'data' => null
            ];
        }

        $result = (bool) $this->factor_payment_interface->deleteFactorPayment($factor->id, $inputs['id']);

        return [
            'result' => $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
