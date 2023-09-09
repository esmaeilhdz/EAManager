<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClothBuyItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cloth_buy_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cloth_buy_id');
            $table->unsignedTinyInteger('color_id')->index();
            $table->string('metre');
            $table->string('price');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('cloth_buy_id')->references('id')->on('cloth_buys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cloth_buy_items');
    }
}
