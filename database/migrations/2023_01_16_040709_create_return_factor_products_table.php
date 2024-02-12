<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnFactorItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('return_factor_items');
        Schema::create('return_factor_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_factor_id');
            $table->unsignedBigInteger('product_warehouse_id');
            $table->unsignedInteger('free_size_count');
            $table->unsignedInteger('size1_count');
            $table->unsignedInteger('size2_count');
            $table->unsignedInteger('size3_count');
            $table->unsignedInteger('size4_count');
            $table->text('description');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('return_factor_id')->references('id')->on('return_factors');
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
        Schema::dropIfExists('return_factor_items');
    }
}
