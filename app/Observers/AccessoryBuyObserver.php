<?php

namespace App\Observers;

use App\Exceptions\ApiException;
use App\Models\Accessory;
use App\Models\AccessoryBuy;
use App\Models\AccessoryWareHouse;
use App\Models\WarehouseItem;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccessoryBuyObserver
{
    use Common;

    public bool $afterCommit = true;

    /**
     * Handle the AccessoryBuy "created" event.
     * @param AccessoryBuy $accessory_buy
     * @return void
     * @throws ApiException
     */
    public function created(AccessoryBuy $accessory_buy)
    {
        $user = Auth::user();
        $company_id = $this->getCurrentCompanyOfUser($user);
        $warehouse_id = $this->getCenterWarehouseOfCompany($company_id);

        $warehouse_item = WarehouseItem::where('warehouse_id', $warehouse_id)
            ->where('model_type', Accessory::class)
            ->where('model_id', $accessory_buy->accessory_id)
            ->where('place_id', $accessory_buy->place_id)
            ->first();

        if (is_null($warehouse_item)) {
            $warehouse_item = new WarehouseItem();

            $warehouse_item->warehouse_id = $warehouse_id;
            $warehouse_item->model_type = Accessory::class;
            $warehouse_item->model_id = $accessory_buy->accessory_id;
            $warehouse_item->place_id = $accessory_buy->place_id;
            $warehouse_item->count = $accessory_buy->count;
        } else {
            $warehouse_item->count += $accessory_buy->count;
        }

        $warehouse_item->save();
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
     * @param AccessoryBuy $accessory_buy
     * @return void
     * @throws ApiException
     */
    public function deleted(AccessoryBuy $accessory_buy)
    {
        $user = Auth::user();
        $company_id = $this->getCurrentCompanyOfUser($user);
        $warehouse_id = $this->getCenterWarehouseOfCompany($company_id);

        $warehouse_item = WarehouseItem::where('warehouse_id', $warehouse_id)
            ->where('model_type', Accessory::class)
            ->where('model_id', $accessory_buy->accessory_id)
            ->where('place_id', $accessory_buy->place_id)
            ->first();

        if (!is_null($warehouse_item)) {
            $warehouse_item->count -= $accessory_buy->count;
            $warehouse_item->save();
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
