<?php

namespace App\Repositories\Interfaces;

interface iClothBuy
{
    public function getClothBuys($inputs);

    public function getClothBuyById($inputs);

    public function editClothBuy($cloth_buy, $inputs);

    public function addClothBuy($inputs, $user);

    public function deleteClothBuy($cloth_buy);
}
