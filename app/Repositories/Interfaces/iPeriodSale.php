<?php

namespace App\Repositories\Interfaces;

interface iPeriodSale
{
    public function getPeriodSales($inputs);

    public function getPeriodSaleById($id);

    public function editPeriodSale($inputs);

    public function addPeriodSale($inputs, $user);

    public function deletePeriodSale($id);
}
