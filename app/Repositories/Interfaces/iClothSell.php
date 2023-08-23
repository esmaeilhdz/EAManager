<?php

namespace App\Repositories\Interfaces;

interface iClothSell
{
    public function getClothSells($inputs);

    public function getClothSellById($inputs);

    public function editClothSell($cloth_sell, $inputs);

    public function addClothSell($inputs, $user);

    public function deleteClothSell($cloth_sell);
}
