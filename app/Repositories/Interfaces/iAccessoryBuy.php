<?php

namespace App\Repositories\Interfaces;

interface iAccessoryBuy
{
    public function getAccessoryBuys($inputs);

    public function getAccessoryBuyById($inputs);

    public function editAccessoryBuy($accessory_buy, $inputs);

    public function addAccessoryBuy($inputs, $user);

    public function deleteAccessoryBuy($accessory_buy);
}
