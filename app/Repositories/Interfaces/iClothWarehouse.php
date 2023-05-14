<?php

namespace App\Repositories\Interfaces;

interface iClothWarehouse
{
    public function getClothWarehouses($inputs);

    public function editWarehouse($inputs);
}
