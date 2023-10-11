<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClothSellItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('cloth_sell_items');
        Schema::create('cloth_sell_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cloth_sell_id');
            $table->unsignedTinyInteger('color_id')->index();
            $table->string('metre');
            $table->string('unit_price');
            $table->string('price');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('cloth_sell_id')->references('id')->on('cloth_sells');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cloth_sell_items');
    }
}
