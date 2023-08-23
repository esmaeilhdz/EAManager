<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClothSellsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('cloth_sells');
        Schema::create('cloth_sells', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cloth_id')->index();
            $table->unsignedBigInteger('customer_id')->comment('خریدار');
            $table->unsignedBigInteger('warehouse_place_id')->index()->comment('انبار');
            $table->unsignedInteger('metre');
            $table->unsignedTinyInteger('roll_count')->comment('تعداد طاقه');
            $table->date('sell_date')->comment('تاریخ دریافت پارچه');
            $table->string('factor_no');
            $table->string('price');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('cloth_id')->references('id')->on('cloths');
            $table->foreign('customer_id')->references('id')->on('customers');
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
        Schema::dropIfExists('cloth_sells');
    }
}
