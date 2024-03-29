<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFactorItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('factor_items');
        Schema::create('factor_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('factor_id');
            $table->unsignedBigInteger('product_warehouse_id');
            $table->unsignedInteger('free_size_count');
            $table->unsignedInteger('size1_count');
            $table->unsignedInteger('size2_count');
            $table->unsignedInteger('size3_count');
            $table->unsignedInteger('size4_count');
            $table->unsignedInteger('price');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('factor_id')->references('id')->on('factors');
            $table->foreign('product_warehouse_id')->references('id')->on('product_warehouses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('factor_items');
    }
}
