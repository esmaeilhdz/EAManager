<?php

namespace App\Repositories\Interfaces;

interface iClothBuyItems
{
    public function getClothBuyItems($inputs);

    public function getClothBuyItemById($inputs);

    public function addClothBuyItem($inputs, $user, $quiet = false);

    public function deleteClothBuyItem($cloth_buy_item);

    public function deleteClothBuyItems($cloth_buy_id);
}
