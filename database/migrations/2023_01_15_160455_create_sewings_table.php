<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSewingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('sewings');
        Schema::create('sewings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('seamstress_person_id')->nullable()->comment('خیاط کارمند داخلی');
            $table->unsignedBigInteger('place_id')->nullable()->comment('مزدی دوز');
            $table->unsignedBigInteger('color_id')->index();
            $table->boolean('is_mozdi_dooz')->default(0);
            $table->unsignedInteger('count');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('product_id')->references('id')->on('products');
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
        Schema::dropIfExists('sewings');
    }
}
