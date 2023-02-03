<?php

namespace App\Observers;

use App\Models\ClothBuy;
use App\Models\ClothWareHouse;

class ClothBuyObserver
{

    public bool $afterCommit = true;

    /**
     * Handle the ClothBuy "created" event.
     *
     * @param ClothBuy $cloth_buy
     * @return void
     */
    public function created(ClothBuy $cloth_buy)
    {
        $cloth_warehouse = ClothWareHouse::where('cloth_id', $cloth_buy->cloth_id)->first();
        if (is_null($cloth_warehouse)) {
            $cloth_warehouse = new ClothWareHouse();

            $cloth_warehouse->cloth_id = $cloth_buy->cloth_id;
            $cloth_warehouse->metre = $cloth_buy->metre;
            $cloth_warehouse->roll_count = $cloth_buy->roll_count;
            $cloth_warehouse->created_by = $cloth_buy->created_by;
        } else {
            $cloth_warehouse->metre += $cloth_buy->metre;
            $cloth_warehouse->roll_count += $cloth_buy->roll_count;
        }

        $cloth_warehouse->save();
    }

    /**
     * Handle the ClothBuy "updated" event.
     *
     * @param ClothBuy $cloth_buy
     * @return void
     */
    public function updated(ClothBuy $cloth_buy)
    {
//        $cloth_warehouse = ClothWareHouse::where('cloth_id', $cloth_buy->cloth_id)->first();

        /*if ($cloth_buy->metre > $cloth_warehouse->metre) {
            $metre = $cloth_buy->metre + $cloth_warehouse->metre;
        } elseif ($cloth_buy->metre < $cloth_warehouse->metre) {
            $metre = $cloth_buy->metre - $cloth_warehouse->metre;
        }

        if ($cloth_buy->roll_count > $cloth_warehouse->roll_count) {
            $roll_count = $cloth_buy->roll_count + $cloth_warehouse->roll_count;
        } elseif ($cloth_buy->roll_count < $cloth_warehouse->roll_count) {
            $roll_count = $cloth_buy->roll_count - $cloth_warehouse->roll_count;
        }*/

        /*$cloth_warehouse->metre = $metre;
        $cloth_warehouse->roll_count = $roll_count;

        $cloth_warehouse->save();*/
    }

    /**
     * Handle the ClothBuy "deleted" event.
     *
     * @param ClothBuy $cloth_buy
     * @return void
     */
    public function deleted(ClothBuy $cloth_buy)
    {
        $cloth_warehouse = ClothWareHouse::where('cloth_id', $cloth_buy->cloth_id)->first();

        if (!is_null($cloth_buy)) {
            $cloth_warehouse->metre -= $cloth_buy->metre;
            $cloth_warehouse->roll_count -= $cloth_buy->roll_count;
            $cloth_warehouse->save();
        } else {
            $cloth_warehouse->delete();
        }

        $cloth_warehouse->save();
    }

    /**
     * Handle the ClothBuy "restored" event.
     *
     * @param ClothBuy $cloth_buy
     * @return void
     */
    public function restored(ClothBuy $cloth_buy)
    {
    }

    /**
     * Handle the ClothBuy "force deleted" event.
     *
     * @param ClothBuy $cloth_buy
     * @return void
     */
    public function forceDeleted(ClothBuy $cloth_buy)
    {
    }
}
