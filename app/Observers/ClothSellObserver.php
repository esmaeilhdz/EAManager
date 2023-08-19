<?php

namespace App\Observers;

use App\Models\ClothSell;
use App\Models\ClothWareHouse;

class ClothSellObserver
{
    /**
     * Handle the ClothSell "created" event.
     *
     * @param ClothSell $clothSell
     * @return void
     */
    public function created(ClothSell $clothSell)
    {
        $cloth_warehouse = ClothWareHouse::where('cloth_id', $clothSell->cloth_id)
            ->where('place_id', $clothSell->warehouse_place_id)
            ->first();

        if (is_null($cloth_warehouse)) {
            $cloth_warehouse = new ClothWareHouse();

            $cloth_warehouse->cloth_id = $clothSell->cloth_id;
            $cloth_warehouse->place_id = $clothSell->warehouse_place_id;
            $cloth_warehouse->metre = $clothSell->metre;
            $cloth_warehouse->roll_count = $clothSell->roll_count;
            $cloth_warehouse->created_by = $clothSell->created_by;
        } else {
            $cloth_warehouse->metre -= $clothSell->metre;
            $cloth_warehouse->roll_count -= $clothSell->roll_count;
        }

        $cloth_warehouse->save();
    }

    /**
     * Handle the ClothSell "updated" event.
     *
     * @param ClothSell $clothSell
     * @return void
     */
    public function updated(ClothSell $clothSell)
    {
        //
    }

    /**
     * Handle the ClothSell "deleted" event.
     *
     * @param ClothSell $clothSell
     * @return void
     */
    public function deleted(ClothSell $clothSell)
    {
        $cloth_warehouse = ClothWareHouse::where('cloth_id', $clothSell->cloth_id)->first();

        if (!is_null($clothSell)) {
            $cloth_warehouse->metre += $clothSell->metre;
            $cloth_warehouse->roll_count += $clothSell->roll_count;
            $cloth_warehouse->save();
        } else {
            $cloth_warehouse->delete();
        }

        $cloth_warehouse->save();
    }

    /**
     * Handle the ClothSell "restored" event.
     *
     * @param ClothSell $clothSell
     * @return void
     */
    public function restored(ClothSell $clothSell)
    {
        //
    }

    /**
     * Handle the ClothSell "force deleted" event.
     *
     * @param ClothSell $clothSell
     * @return void
     */
    public function forceDeleted(ClothSell $clothSell)
    {
        //
    }
}
