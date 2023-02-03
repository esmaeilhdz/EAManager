<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductToStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('product_to_stores');
        Schema::create('product_to_stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_warehouse_id');
            $table->unsignedBigInteger('request_product_warehouse_id')->nullable();
            $table->unsignedInteger('free_size_count');
            $table->unsignedInteger('size1_count');
            $table->unsignedInteger('size2_count');
            $table->unsignedInteger('size3_count');
            $table->unsignedInteger('size4_count');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('product_warehouse_id')->references('id')->on('product_warehouses');
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
        Schema::dropIfExists('product_to_stores');
    }
}
