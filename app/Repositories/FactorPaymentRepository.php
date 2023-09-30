<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\FactorPayment;
use App\Traits\Common;

class FactorPaymentRepository implements Interfaces\iFactorPayment
{
    use Common;

    public function getById($factor_id, $id, $select = [], $relation = [])
    {
        try {
            $factor_payment = FactorPayment::where('factor_id', $factor_id)
                ->where('id', $id);

            if ($select) {
                $factor_payment = $factor_payment->select($select);
            }

            if ($relation) {
                $factor_payment = $factor_payment->with($relation);
            }

            return $factor_payment->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    public function getByFactorId($factor_id, $inputs, $select = [], $relation = [])
    {
        try {
            $factor_payment = FactorPayment::where('factor_id', $factor_id);

            if ($select) {
                $factor_payment = $factor_payment->select($select);
            }

            if ($relation) {
                $factor_payment = $factor_payment->with($relation);
            }

            return $factor_payment->orderByDesc('id')
                ->paginate($inputs['per_page']);
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * ویرایش محصول فاکتور
     * @param $factor_payment
     * @param $inputs
     * @return mixed
     * @throws ApiException
     */
    public function editFactorPayment($factor_payment, $inputs): mixed
    {
        try {
            $factor_payment->payment_warehouse_id = $inputs['payment_warehouse_id'];
            $factor_payment->free_size_count = $inputs['free_size_count'];
            $factor_payment->size1_count = $inputs['size1_count'];
            $factor_payment->size2_count = $inputs['size2_count'];
            $factor_payment->size3_count = $inputs['size3_count'];
            $factor_payment->size4_count = $inputs['size4_count'];
            $factor_payment->price = $inputs['price'];

            return $factor_payment->save();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * افزودن محصول فاکتور
     * @param array $inputs
     * @param $factor_id
     * @param $user
     * @return array
     * @throws ApiException
     */
    public function addFactorPayment(array $inputs, $factor_id, $user): array
    {
        try {
            $factor_payment = new FactorPayment();

            $factor_payment->factor_id = $factor_id;
            $factor_payment->payment_type_id = $inputs['payment_type_id'];
            $factor_payment->price = $inputs['price'];
            $factor_payment->description = $inputs['description'];

            $result = $factor_payment->save();

            return [
                'result' => $result,
                'data' => $result ? $factor_payment->id : null
            ];

        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف محصولات فاکتور
     * @param $factor_id
     * @return mixed
     * @throws ApiException
     */
    public function deleteFactorPayments($factor_id): mixed
    {
        try {
            return FactorPayment::where('factor_id', $factor_id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * حذف محصول فاکتور
     * @param $factor_id
     * @param $id
     * @return mixed
     * @throws ApiException
     */
    public function deleteFactorPayment($factor_id, $id): mixed
    {
        try {
            return FactorPayment::where('factor_id', $factor_id)->where('id', $id)->delete();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }
}
