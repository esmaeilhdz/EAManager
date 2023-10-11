<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('product_accessories');
        Schema::create('product_accessories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->morphs('model');
            $table->float('amount');
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
        Schema::dropIfExists('product_accessories');
    }
}
