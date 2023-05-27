<?php

namespace App\Repositories\Interfaces;

interface iSalaryDeduction
{
    public function getSalaryDeductions($inputs, $user);

    public function getSalaryDeductionDetail($inputs, $user, $relation = []);

    public function editSalaryDeduction($salary_deduction, $inputs);

    public function addSalaryDeduction($inputs, $user);

    public function deleteSalaryDeduction($salary_deduction);
}
