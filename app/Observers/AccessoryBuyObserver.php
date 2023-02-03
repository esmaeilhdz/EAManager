<?php

namespace App\Observers;

use App\Models\AccessoryBuy;
use App\Models\AccessoryWareHouse;
use Illuminate\Support\Facades\Log;

class AccessoryBuyObserver
{

    public bool $afterCommit = true;

    /**
     * Handle the AccessoryBuy "created" event.
     *
     * @param AccessoryBuy $accessory_buy
     * @return void
     */
    public function created(AccessoryBuy $accessory_buy)
    {
        $accessory_warehouse = AccessoryWareHouse::where('accessory_id', $accessory_buy->accessory_id)->first();
        if (is_null($accessory_warehouse)) {
            $accessory_warehouse = new AccessoryWareHouse();

            $accessory_warehouse->accessory_id = $accessory_buy->accessory_id;
            $accessory_warehouse->count = $accessory_buy->count;
        } else {
            $accessory_warehouse->count += $accessory_buy->count;
        }

        $accessory_warehouse->save();
    }

    /**
     * Handle the AccessoryBuy "updated" event.
     *
     * @param AccessoryBuy $accessory_buy
     * @return void
     */
    public function updated(AccessoryBuy $accessory_buy)
    {
        //
    }

    /**
     * Handle the AccessoryBuy "deleted" event.
     *
     * @param AccessoryBuy $accessory_buy
     * @return void
     */
    public function deleted(AccessoryBuy $accessory_buy)
    {
        $accessory_warehouse = AccessoryWareHouse::where('accessory_id', $accessory_buy->accessory_id)->first();

        if (!is_null($accessory_buy)) {
            $accessory_warehouse->count -= $accessory_buy->count;
            $accessory_warehouse->save();
        } else {
            $accessory_warehouse->delete();
        }

    }

    /**
     * Handle the AccessoryBuy "restored" event.
     *
     * @param AccessoryBuy $accessory_buy
     * @return void
     */
    public function restored(AccessoryBuy $accessory_buy)
    {
        //
    }

    /**
     * Handle the AccessoryBuy "force deleted" event.
     *
     * @param AccessoryBuy $accessory_buy
     * @return void
     */
    public function forceDeleted(AccessoryBuy $accessory_buy)
    {
        //
    }
}
