<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAccessoryPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('product_accessory_prices');
        Schema::create('product_accessory_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_accessory_id');
            $table->unsignedBigInteger('product_price_id');
            $table->unsignedInteger('price');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('product_accessory_id')->references('id')->on('product_accessories');
            $table->foreign('product_price_id')->references('id')->on('product_prices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_accessory_prices');
    }
}
