<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClothBuysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('cloth_buys');
        Schema::create('cloth_buys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cloth_id')->index();
            $table->unsignedBigInteger('seller_place_id')->index()->comment('فروشنده');
            $table->unsignedBigInteger('warehouse_place_id')->index()->comment('انبار');
            $table->unsignedInteger('metre');
            $table->unsignedTinyInteger('roll_count')->comment('تعداد طاقه');
            $table->date('receive_date')->comment('تاریخ دریافت پارچه');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('cloth_id')->references('id')->on('cloths');
            $table->foreign('seller_place_id')->references('id')->on('places');
            $table->foreign('warehouse_place_id')->references('id')->on('places');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cloth_buys');
    }
}
