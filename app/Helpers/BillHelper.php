<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\Repositories\Interfaces\iBill;
use App\Repositories\Interfaces\iPayment;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillHelper
{
    use Common;

    // attributes
    public iBill $bill_interface;
    public iPayment $payment_interface;

    public function __construct(
        iBill $bill_interface,
        iPayment $payment_interface
    )
    {
        $this->bill_interface = $bill_interface;
        $this->payment_interface = $payment_interface;
    }

    /**
     * لیست قبض ها
     * @param $inputs
     * @return array
     */
    public function getBills($inputs): array
    {
        $user = Auth::user();
        $search_data = $param_array = [];
        $search_data[] = $this->GWC($inputs['search_txt'] ?? '', 'numeric:bill_type_id,string:bill_id;payment_id');
        $inputs['where']['search']['condition'] = $this->generateWhereCondition($search_data, $param_array);
        $inputs['where']['search']['params'] = $param_array;

        $inputs['order_by'] = $this->orderBy($inputs, 'bills');
        $inputs['per_page'] = $this->calculatePerPage($inputs);

        $bills = $this->bill_interface->getBills($inputs, $user);

        $bills->transform(function ($item) {
            return [
                'id' => $item->id,
                'bill_type' => [
                    'id' => $item->bill_type_id,
                    'caption' => $item->bill_type->enum_caption
                ],
                'bill_id' => $item->bill_id,
                'payment_id' => $item->payment_id,
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
            'data' => $bills
        ];
    }

    /**
     * جزئیات قبض
     * @param $id
     * @return array
     */
    public function getBillDetail($id): array
    {
        $user = Auth::user();
        $select = ['id', 'bill_type_id', 'bill_id', 'payment_id'];
        $relation = [
            'bill_type:enum_id,enum_caption',
            'payment:model_type,model_id,account_id,payment_date,payment_tracking_code,description,price',
            'payment.account:id,bank_id',
            'payment.account.bank:enum_id,enum_caption',
        ];
        $bill = $this->bill_interface->getBillById($id, $user, $select, $relation);
        if (is_null($bill)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        return [
            'result' => true,
            'message' => __('messages.success'),
            'data' => $bill
        ];
    }

    /**
     * ویرایش قبض
     * @param $inputs
     * @return array
     */
    public function editBill($inputs): array
    {
        $user = Auth::user();
        $bill = $this->bill_interface->getBillById($inputs['id'], $user);
        if (is_null($bill)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $result = $this->bill_interface->editBill($bill, $inputs);
        return [
            'result' => (bool) $result,
            'message' => $result ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

    /**
     * افزودن قبض
     * @param $inputs
     * @return array
     */
    public function addBill($inputs): array
    {
        $user = Auth::user();
        $result = $this->bill_interface->addBill($inputs, $user);
        return [
            'result' => $result['result'],
            'message' => $result['result'] ? __('messages.success') : __('messages.fail'),
            'data' => $result['data']
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function deleteBill($id): array
    {
        $user = Auth::user();
        $bill = $this->bill_interface->getBillById($id, $user, ['id']);
        if (is_null($bill)) {
            return [
                'result' => false,
                'message' => __('messages.record_not_found'),
                'data' => null
            ];
        }

        $inputs['model_type'] = $this->convertModelNameToNamespace('bill');
        $inputs['model_id'] = $id;
        DB::beginTransaction();
        $result[] = $this->bill_interface->deleteBill($id);
        $result[] = $this->payment_interface->deletePaymentsResource($inputs, $user);

        if (!in_array(false, $result)) {
            $flag = true;
            DB::commit();
        } else {
            $flag = false;
            DB::rollBack();
        }

        return [
            'result' => $flag,
            'message' => $flag ? __('messages.success') : __('messages.fail'),
            'data' => null
        ];
    }

}
