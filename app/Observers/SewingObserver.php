<?php

namespace App\Observers;

use App\Models\ProductWarehouse;
use App\Models\Sewing;

class SewingObserver
{
    /**
     * Handle the Sewing "created" event.
     *
     * @param Sewing $sewing
     * @return void
     */
    public function created(Sewing $sewing)
    {
        $product_warehouse = ProductWarehouse::where('product_id', $sewing->product_id)
            ->where('color_id', $sewing->color_id)
            ->first();

        if (is_null($product_warehouse)) {
            $product_warehouse->product_id = $sewing['product_id'];
            $product_warehouse->color_id = $sewing['color_id'];
            $product_warehouse->place_id = 1;
            $product_warehouse->free_size_count = $sewing->count;
            $product_warehouse->size1_count = $sewing->count;
            $product_warehouse->size2_count = $sewing->count;
            $product_warehouse->size3_count = $sewing->count;
            $product_warehouse->size4_count = $sewing->count;
            $product_warehouse->created_by = $sewing->created_by;
        } else {
            $product_warehouse->free_size_count += $sewing->count;
            $product_warehouse->size1_count += $sewing->count;
            $product_warehouse->size2_count += $sewing->count;
            $product_warehouse->size3_count += $sewing->count;
            $product_warehouse->size4_count += $sewing->count;
        }

        $product_warehouse->save();

    }

    /**
     * Handle the Sewing "updated" event.
     *
     * @param Sewing $sewing
     * @return void
     */
    public function updated(Sewing $sewing)
    {
    }

    /**
     * Handle the Sewing "deleted" event.
     *
     * @param Sewing $sewing
     * @return void
     */
    public function deleted(Sewing $sewing)
    {
        $count = floor($sewing->count / 5);
        $product_warehouse = ProductWarehouse::where('product_id', $sewing->product_id)
            ->where('color_id', $sewing->color_id)
            ->first();

        $product_warehouse->free_size_count -= $count;
        $product_warehouse->size1_count -= $count;
        $product_warehouse->size2_count -= $count;
        $product_warehouse->size3_count -= $count;
        $product_warehouse->size4_count -= $count;

        $product_warehouse->save();
    }

    /**
     * Handle the Sewing "restored" event.
     *
     * @param Sewing $sewing
     * @return void
     */
    public function restored(Sewing $sewing)
    {
        //
    }

    /**
     * Handle the Sewing "force deleted" event.
     *
     * @param Sewing $sewing
     * @return void
     */
    public function forceDeleted(Sewing $sewing)
    {
        //
    }
}
