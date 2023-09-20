<?php

namespace App\Observers;

use App\Models\ClothSellItem;
use App\Models\ClothWareHouse;

class ClothSellObserver
{
    /**
     * Handle the ClothSell "created" event.
     *
     * @param ClothSellItem $clothSellItem
     * @return void
     */
    public function created(ClothSellItem $clothSellItem)
    {
        $cloth_warehouse = ClothWareHouse::where('cloth_id', $clothSellItem->cloth_sell->cloth_id)
            ->where('place_id', $clothSellItem->cloth_sell->warehouse_place_id)
            ->where('color_id', $clothSellItem->color_id)
            ->first();

        $cloth_warehouse->metre -= intval($clothSellItem->metre);

        $cloth_warehouse->save();
    }

    /**
     * Handle the ClothSell "updated" event.
     *
     * @param ClothSellItem $clothSellItem
     * @return void
     */
    public function updated(ClothSellItem $clothSellItem)
    {
        //
    }

    /**
     * Handle the ClothSell "deleted" event.
     *
     * @param ClothSellItem $clothSellItem
     * @return void
     */
    public function deleted(ClothSellItem $clothSellItem)
    {
        $cloth_warehouse = ClothWareHouse::where('cloth_id', $clothSellItem->cloth_sell->cloth_id)
            ->where('place_id', $clothSellItem->cloth_sell->warehouse_place_id)
            ->where('color_id', $clothSellItem->color_id)->first();

        if (!is_null($cloth_warehouse)) {
            $cloth_warehouse->metre += $clothSellItem->metre;
            $cloth_warehouse->save();
        } else {
            $cloth_warehouse->delete();
        }
    }

    /**
     * Handle the ClothSell "restored" event.
     *
     * @param ClothSellItem $clothSellItem
     * @return void
     */
    public function restored(ClothSellItem $clothSellItem)
    {
        //
    }

    /**
     * Handle the ClothSell "force deleted" event.
     *
     * @param ClothSellItem $clothSellItem
     * @return void
     */
    public function forceDeleted(ClothSellItem $clothSellItem)
    {
        //
    }
}
