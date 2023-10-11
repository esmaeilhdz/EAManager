<?php

namespace App\Repositories\Interfaces;

interface iClothWarehouse
{
    public function getClothWarehouses($inputs);

    public function getClothWarehousesByCloth($cloth_id, $color_id, $place_id);

    public function addWarehouse($inputs, $user);

    public function editWarehouse($inputs);

    public function editWarehouseMetre($inputs);

    public function editWarehouseRollCount($inputs);
}
