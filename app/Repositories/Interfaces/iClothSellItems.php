<?php

namespace App\Repositories\Interfaces;

interface iClothSellItems
{
    public function getClothSellItems($inputs);

    public function getClothSellItemById($inputs);

    public function addClothSellItem($inputs, $user, $quiet = false);

    public function deleteClothSellItem($cloth_sell_item);

    public function deleteClothSellItems($cloth_sell_id);
}
