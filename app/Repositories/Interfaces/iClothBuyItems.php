<?php

namespace App\Repositories\Interfaces;

interface iClothBuyItems
{
    public function getClothBuyItems($inputs);

    public function getClothBuyItemById($inputs);

    public function addClothBuyItem($inputs, $user);

    public function deleteClothBuyItem($cloth_buy_item);
}
