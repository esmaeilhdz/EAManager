<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('place_id')->comment('فروشنده');
            $table->morphs('model');
            $table->unsignedInteger('free_size_count')->nullable();
            $table->unsignedInteger('size1_count')->nullable();
            $table->unsignedInteger('size2_count')->nullable();
            $table->unsignedInteger('size3_count')->nullable();
            $table->unsignedInteger('size4_count')->nullable();
            $table->unsignedFloat('metre')->nullable()->comment('متراژ پارچه یا برخی خرج کارها');
            $table->unsignedInteger('count')->nullable()->comment('تعداد خرج کار');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('place_id')->references('id')->on('places');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_items');
    }
}
