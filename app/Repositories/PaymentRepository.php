<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Payment;
use App\Traits\Common;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class PaymentRepository implements Interfaces\iPayment
{
    use Common;

    /**
     * لیست پرداخت ها
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getPayments($inputs): LengthAwarePaginator
    {
        try {
            $user = Auth::user();
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Payment::with([
                'creator:id,person_id',
                'creator.person:id,name,family',
                'payment_type:enum_id,enum_caption',
                'gate:enum_id,enum_caption',
                'account:id,branch_name'
            ])
                ->select([
                    'id',
                    'payment_type_id',
                    'gate_id',
                    'account_id',
                    'price',
                    'payment_date',
                    'payment_tracking_code',
                    'created_by',
                    'created_at'
                ])
                ->where('company_id', $company_id)
                ->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * پرداخت ها برای یک منبع
     * @param $inputs
     * @return LengthAwarePaginator
     * @throws ApiException
     */
    public function getPaymentsResource($inputs): LengthAwarePaginator
    {
        try {
            $user = Auth::user();
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Payment::with([
                'creator:id,person_id',
                'creator.person:id,name,family',
                'payment_type:enum_id,enum_caption',
                'gate:enum_id,enum_caption',
                'account:id,branch_name'
            ])
                ->select([
                    'id',
                    'payment_type_id',
                    'gate_id',
                    'account_id',
                    'price',
                    'payment_date',
                    'payment_tracking_code',
                    'created_by',
                    'created_at'
                ])
                ->where('company_id', $company_id)
                ->where('model_type', $inputs['model_type'])
                ->where('model_id', $inputs['model_id'])
                ->where('company_id', $company_id)
                ->whereRaw($inputs['where']['search']['condition'], $inputs['where']['search']['params'])
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getPaymentById($inputs, $select = [], $relation = [])
    {
        try {
            $payment = Payment::where('id', $inputs['id'])
                ->where('model_type', $inputs['model_type'])
                ->where('model_id', $inputs['model_id']);

            if (count($select)) {
                $payment = $payment->select($select);
            }

            if (count($relation)) {
                $payment = $payment->with($relation);
            }

            return $payment->first();

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function editPayment($payment, $inputs)
    {
        try {
            $payment->account_id = $inputs['account_id'];
            $payment->payment_type_id = $inputs['payment_type_id'];
            $payment->gate_id = $inputs['gate_id'];
            $payment->price = $inputs['price'];
            $payment->payment_date = $inputs['payment_date'];
            $payment->payment_tracking_code = $inputs['payment_tracking_code'];
            $payment->description = $inputs['description'];

            return $payment->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function addPayment($inputs, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            $payment = new Payment();

            $payment->model_type = $inputs['model_type'];
            $payment->model_id = $inputs['model_id'];
            $payment->company_id = $company_id;
            $payment->account_id = $inputs['account_id'];
            $payment->payment_type_id = $inputs['payment_type_id'];
            $payment->gate_id = $inputs['gate_id'];
            $payment->price = $inputs['price'];
            $payment->payment_date = $inputs['payment_date'];
            $payment->payment_tracking_code = $inputs['payment_tracking_code'];
            $payment->description = $inputs['description'];
            $payment->created_by = $user->id;

            $result = $payment->save();

            return [
                'result' => $result,
                'data' => $result ? $payment->id : null
            ];
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deletePayment($payment)
    {
        try {
            return $payment->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function deletePaymentsResource($inputs, $user)
    {
        try {
            $company_id = $this->getCurrentCompanyOfUser($user);
            return Payment::where('model_type', $inputs['model_type'])
                ->where('model_id', $inputs['model_id'])
                ->where('company_id', $company_id)
                ->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
