<?php

namespace App\Observers;

use App\Models\ClothBuyItem;
use App\Models\ClothWareHouse;

class ClothBuyObserver
{

    public bool $afterCommit = true;

    /**
     * Handle the ClothBuy "created" event.
     *
     * @param ClothBuyItem $cloth_buy_item
     * @return void
     */
    public function created(ClothBuyItem $cloth_buy_item)
    {
        $cloth_warehouse = ClothWareHouse::where('cloth_id', $cloth_buy_item->cloth_buy->cloth_id)
            ->where('place_id', $cloth_buy_item->cloth_buy->warehouse_place_id)
            ->where('color_id', $cloth_buy_item->color_id)
            ->first();
        if (is_null($cloth_warehouse)) {
            $cloth_warehouse = new ClothWareHouse();

            $cloth_warehouse->cloth_id = $cloth_buy_item->cloth_buy->cloth_id;
            $cloth_warehouse->place_id = $cloth_buy_item->cloth_buy->warehouse_place_id;
            $cloth_warehouse->color_id = $cloth_buy_item->color_id;
            $cloth_warehouse->metre = $cloth_buy_item->metre;
            $cloth_warehouse->created_by = $cloth_buy_item->cloth_buy->created_by;
        } else {
            $cloth_warehouse->metre += $cloth_buy_item->metre;
        }

        $cloth_warehouse->save();
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
     */
    public function deleted(ClothBuyItem $cloth_buy_item)
    {
        $cloth_warehouse = ClothWareHouse::where('cloth_id', $cloth_buy_item->cloth_buy->cloth_id)
            ->where('color_id', $cloth_buy_item->color_id)
            ->first();

        if (!is_null($cloth_buy_item)) {
            $cloth_warehouse->metre -= $cloth_buy_item->metre;
            $cloth_warehouse->save();
        } else {
            $cloth_warehouse->delete();
        }

        $cloth_warehouse->save();
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
