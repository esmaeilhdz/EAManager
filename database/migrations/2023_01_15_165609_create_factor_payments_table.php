<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFactorPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('factor_payments');
        Schema::create('factor_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('factor_id');
            $table->unsignedTinyInteger('payment_type_id')->index();
            $table->unsignedInteger('price');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('factor_id')->references('id')->on('factors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('factor_payments');
    }
}
