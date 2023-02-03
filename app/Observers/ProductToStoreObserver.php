<?php

namespace App\Observers;

use App\Models\ProductToStore;
use App\Models\ProductWarehouse;

class ProductToStoreObserver
{

    public bool $afterCommit = true;

    /**
     * Handle the ProductToStore "created" event.
     *
     * @param ProductToStore $product_to_store
     * @return void
     */
    public function created(ProductToStore $product_to_store)
    {
        $product_warehouse = ProductWarehouse::where('id', $product_to_store->product_warehouse_id)->first();

        $flag = true;
        if ($product_warehouse->free_size_count - $product_to_store->free_size_count < 0) {
            $flag = false;
        } elseif ($product_warehouse->size1_count - $product_to_store->size1_count < 0) {
            $flag = false;
        } elseif ($product_warehouse->size2_count - $product_to_store->size2_count < 0) {
            $flag = false;
        } elseif ($product_warehouse->size3_count - $product_to_store->size3_count < 0) {
            $flag = false;
        } elseif ($product_warehouse->size4_count - $product_to_store->size4_count < 0) {
            $flag = false;
        }

        if ($flag) {
            $product_warehouse->free_size_count -= $product_to_store->free_size_count;
            $product_warehouse->size1_count -= $product_to_store->size1_count;
            $product_warehouse->size2_count -= $product_to_store->size2_count;
            $product_warehouse->size3_count -= $product_to_store->size3_count;
            $product_warehouse->size4_count -= $product_to_store->size4_count;

            $product_warehouse->save();
        }
    }

    /**
     * Handle the ProductToStore "updated" event.
     *
     * @param ProductToStore $product_to_store
     * @return void
     */
    public function updated(ProductToStore $product_to_store)
    {
        //
    }

    /**
     * Handle the ProductToStore "deleted" event.
     *
     * @param ProductToStore $product_to_store
     * @return void
     */
    public function deleted(ProductToStore $product_to_store)
    {
        //
    }

    /**
     * Handle the ProductToStore "restored" event.
     *
     * @param ProductToStore $product_to_store
     * @return void
     */
    public function restored(ProductToStore $product_to_store)
    {
        //
    }

    /**
     * Handle the ProductToStore "force deleted" event.
     *
     * @param ProductToStore $product_to_store
     * @return void
     */
    public function forceDeleted(ProductToStore $product_to_store)
    {
        //
    }
}
