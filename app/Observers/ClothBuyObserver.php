<?php

namespace App\Observers;

use App\Exceptions\ApiException;
use App\Models\Cloth;
use App\Models\ClothBuy;
use App\Models\ClothBuyItem;
use App\Models\ClothWareHouse;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;

class ClothBuyObserver
{
    use Common;

    public bool $afterCommit = true;

    /**
     * Handle the ClothBuy "created" event.
     *
     * @param ClothBuyItem $cloth_buy_item
     * @return void
     * @throws ApiException
     */
    public function created(ClothBuyItem $cloth_buy_item)
    {
        $user = Auth::user();
        $company_id = $this->getCurrentCompanyOfUser($user);
        $warehouse_id = $this->getCenterWarehouseOfCompany($company_id);

        $warehouse_item = WarehouseItem::where('warehouse_id', $warehouse_id)
            ->where('model_type', Cloth::class)
            ->where('model_id', $cloth_buy_item->cloth_buy->cloth_id)
            ->where('place_id', $cloth_buy_item->cloth_buy->warehouse_place_id)
            ->where('color_id', $cloth_buy_item->color_id)
            ->first();

        if (is_null($warehouse_item)) {
            $warehouse_item = new WarehouseItem();

            $warehouse_item->warehouse_id = $warehouse_id;
            $warehouse_item->model_type = Cloth::class;
            $warehouse_item->model_id = $cloth_buy_item->cloth_buy->cloth_id;
            $warehouse_item->place_id = $cloth_buy_item->cloth_buy->warehouse_place_id;
            $warehouse_item->color_id = $cloth_buy_item->color_id;
            $warehouse_item->metre = $cloth_buy_item->metre;
        } else {
            $warehouse_item->metre += $cloth_buy_item->metre;
        }

        $warehouse_item->save();
    }

    /**
     * Handle the ClothBuy "updated" event.
     *
     * @param ClothBuyItem $cloth_buy_item
     * @return void
     */
    public function updated(ClothBuyItem $cloth_buy_item)
    {
    }

    /**
     * Handle the ClothBuy "deleted" event.
     *
     * @param ClothBuyItem $cloth_buy_item
     * @return void
     * @throws ApiException
     */
    public function deleted(ClothBuyItem $cloth_buy_item)
    {
        $user = Auth::user();
        $company_id = $this->getCurrentCompanyOfUser($user);
        $warehouse_id = $this->getCenterWarehouseOfCompany($company_id);

        $warehouse_item = WarehouseItem::where('warehouse_id', $warehouse_id)
            ->where('model_type', Cloth::class)
            ->where('model_id', $cloth_buy_item->cloth_buy->cloth_id)
            ->where('place_id', $cloth_buy_item->cloth_buy->warehouse_place_id)
            ->where('color_id', $cloth_buy_item->color_id)
            ->first();

        if (!is_null($warehouse_item)) {
            $warehouse_item->metre -= $cloth_buy_item->metre;
            $warehouse_item->save();
        }
    }

    /**
     * Handle the ClothBuy "restored" event.
     *
     * @param ClothBuyItem $cloth_buy_item
     * @return void
     */
    public function restored(ClothBuyItem $cloth_buy_item)
    {
    }

    /**
     * Handle the ClothBuy "force deleted" event.
     *
     * @param ClothBuy $cloth_buy_item
     * @return void
     */
    public function forceDeleted(ClothBuy $cloth_buy_item)
    {
    }
}
