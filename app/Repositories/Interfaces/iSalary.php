<?php

namespace App\Repositories\Interfaces;

interface iSalary
{
    public function getAllSalaries($inputs, $user);

    public function getSalaries($inputs, $user);

    public function getSalaryDetail($inputs, $user, $select = [], $relation = []);

    public function getSalaryById($id, $user, $select = [], $relation = []);

    public function editSalary($salary, $inputs);

    public function addSalary($inputs, $user);
}
