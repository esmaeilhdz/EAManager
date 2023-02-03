<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSewingAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('sewing_accessories');
        Schema::create('sewing_accessories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sewing_id');
            $table->unsignedBigInteger('accessory_id');
            $table->unsignedInteger('accessory_count');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('sewing_id')->references('id')->on('sewings');
            $table->foreign('accessory_id')->references('id')->on('accessories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sewing_accessories');
    }
}
