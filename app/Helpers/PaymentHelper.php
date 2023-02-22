<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\Repositories\Interfaces\iPayment;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class PaymentHelper
{
    use Common;

    // attributes
    public iPayment $payment_interface;

    public function __construct(iPayment $payment_interface)
    {
        $this->payment_interface = $payment_interface;
    }

    /**
     * لیست همه پرداخت ها
     * @param $inputs
     * @return array
     */
    public function getPayments($inputs): array
    {
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'numeric:account_id, payment_type_id, gate_id');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'payments');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $payments = $this->payment_interface->getPayments($inputs);

        $payments->transform(function ($item) {
            return [
                'id' => $item->id,
                'account' => $item->account->branch_name,
                'payment_date' => $item->payment_date,
                'price' => $item->price,
                'gate' => is_null($item->gate) ? null : [
                    'id' => $item->gate_id,
                    'caption' => $item->gate->enum_caption
                ],
                'payment_tracking_code' => $item->payment_tracking_code,
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
            'data' => $payments
        ];
    }

    /**
     * لیست همه پرداخت های یک منبع
     * @param $inputs
     * @return array
     * @throws ApiException
     */
    public function getPaymentsResource($inputs): array
    {
        $inputs['model_type'] = $this->convertModelNameToNamespace($inputs['resource']);
        $inputs['model_id'] = $this->getResourceId($inputs['resource'], $inputs['resource_id']);

        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'numeric:account_id, payment_type_id, gate_id');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'payments');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $payments = $this->payment_interface->getPaymentsResource($inputs);

        $payments->transform(function ($item) {
            return [
                'id' => $item->id,
                'account' => $item->account->branch_name,
                'payment_date' => $item->payment_date,
                'price' => $item->price,
                'gate' => is_null($item->gate) ? null : [
                    'id' => $item->gate_id,
                    'caption' => $item->gate->enum_caption
                ],
                'payment_tracking_code' => $item->payment_tracking_code,
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
            'data' => $payments
        ];
    }

    /**
     * جزئیات پرداخت
     * @param $inputs
     * @return array
     * @throws ApiException
     */
    public function getPaymentDetail($inputs): array
    {
        $inputs['model_type'] = $this->convertModelNameToNamespace($inputs['resource']);
        $inputs['model_id'] = $this->getResourceId($inputs['resource'], $inputs['resource_id']);

        $select = ['id', 'payment_type_id', 'gate_id', 'account_id', 'price', 'payment_date', 'payment_tracking_code', 'description'];
        $relation = [
            'payment_type:enum_id,enum_caption',
            'gate:enum_id,enum_caption',
            'account:id,branch_name'
        ];
        $payment = $this->payment_interface->getPaymentById($inputs, $select, $relation);
        if (is_null($payment)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $payment
        ];
    }

    /**
     * ویرایش پرداخت
     * @param $inputs
     * @return array
     * @throws ApiException
     */
    public function editPayment($inputs): array
    {
        $inputs['model_type'] = $this->convertModelNameToNamespace($inputs['resource']);
        $inputs['model_id'] = $this->getResourceId($inputs['resource'], $inputs['resource_id']);

        $payment = $this->payment_interface->getPaymentById($inputs);
        if (is_null($payment)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->payment_interface->editPayment($payment, $inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * افزودن پرداخت
     * @param $inputs
     * @return array
     * @throws ApiException
     */
    public function addPayment($inputs): array
    {
        $inputs['model_type'] = $this->convertModelNameToNamespace($inputs['resource']);
        $inputs['model_id'] = $this->getResourceId($inputs['resource'], $inputs['resource_id']);

        $data = $inputs['model_type']::select('id')->find($inputs['model_id']);
        if (is_null($data)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $user = Auth::user();
        $result = $this->payment_interface->addPayment($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * @param $inputs
     * @return array
     * @throws ApiException
     */
    public function deletePayment($inputs): array
    {
        $inputs['model_type'] = $this->convertModelNameToNamespace($inputs['resource']);
        $inputs['model_id'] = $this->getResourceId($inputs['resource'], $inputs['resource_id']);

        $payment = $this->payment_interface->getPaymentById($inputs, ['id']);
        if (is_null($payment)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->payment_interface->deletePayment($payment);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * @param $inputs
     * @return array
     * @throws ApiException
     */
    public function deletePaymentsResource($inputs): array
    {
        $user = Auth::user();
        $inputs['model_type'] = $this->convertModelNameToNamespace($inputs['resource']);
        $inputs['model_id'] = $this->getResourceId($inputs['resource'], $inputs['resource_id']);

        $result = $this->payment_interface->deletePaymentsResource($inputs, $user);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
