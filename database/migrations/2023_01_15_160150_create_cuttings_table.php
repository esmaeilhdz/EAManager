<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuttingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('cuttings');
        Schema::create('cuttings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('cutted_count');
            $table->unsignedInteger('free_size_count');
            $table->unsignedInteger('size1_count');
            $table->unsignedInteger('size2_count');
            $table->unsignedInteger('size3_count');
            $table->unsignedInteger('size4_count');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuttings');
    }
}
