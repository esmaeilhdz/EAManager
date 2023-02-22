<?php

namespace App\Repositories\Interfaces;

interface iBill
{
    public function getBills($inputs, $user);

    public function getBillById($id, $user, $select = [], $relation = []);

    public function editBill($bill, $inputs);

    public function addBill($inputs, $user);

    public function deleteBill($id);
}
