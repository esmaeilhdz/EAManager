<?php

namespace App\Repositories\Interfaces;

interface iSalePeriod
{
    public function getSalePeriods($inputs);

    public function getSalePeriodById($id);

    public function getSalePeriodsCombo($inputs, $user);

    public function editSalePeriod($inputs);

    public function addSalePeriod($inputs, $user);

    public function deleteSalePeriod($id);
}
