<?php

namespace App\Repositories\Interfaces;

interface iFactorPayment
{
    public function getById($factor_id, $id, $select = [], $relation = []);

    public function editFactorPayment($factor_payment, $inputs);

    public function addFactorPayment(array $inputs, $factor_id, $user);

    public function deleteFactorPayment($factor_id);
}
