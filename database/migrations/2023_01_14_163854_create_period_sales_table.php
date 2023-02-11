<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalePeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('sale_periods');
        Schema::create('sale_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_periods');
    }
}
