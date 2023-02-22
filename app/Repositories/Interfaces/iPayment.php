<?php

namespace App\Repositories\Interfaces;

interface iPayment
{
    public function getPayments($inputs);

    public function getPaymentsResource($inputs);

    public function getPaymentById($inputs, $select = [], $relation = []);

    public function editPayment($payment, $inputs);

    public function addPayment($inputs, $user);

    public function deletePayment($payment);

    public function deletePaymentsResource($inputs, $user);
}
