<?php

namespace App\Repositories\Interfaces;

interface iCustomer
{
    public function getCustomers($inputs);

    public function getCustomerByCode($code, $select = [], $relation = []);

    public function editCustomer($customer, $inputs);

    public function addCustomer($inputs, $user);

    public function deleteCustomer($customer);
}
