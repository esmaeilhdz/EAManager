<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClothWareHousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('cloth_warehouses');
        Schema::create('cloth_warehouses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cloth_id');
            $table->unsignedBigInteger('place_id');
            $table->unsignedInteger('metre');
            $table->unsignedInteger('roll_count')->comment('تعداد طاقه');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('cloth_id')->references('id')->on('cloths');
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
        Schema::dropIfExists('cloth_warehouses');
    }
}
