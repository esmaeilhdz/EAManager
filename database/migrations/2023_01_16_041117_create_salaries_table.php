<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('salaries');
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('person_id');
            $table->date('from_date');
            $table->date('to_date');
            $table->unsignedInteger('reward_price')->comment('پاداش');
            $table->unsignedTinyInteger('overtime_hour')->comment('ساعت اضافه کار');
            $table->unsignedInteger('salary_deduction')->comment('کسر حقوق');
            $table->unsignedTinyInteger('gate_id')->comment('درگاه بانک');
            $table->string('payment_tracking_code', 50)->comment('کد پیگیری پرداخت');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('person_id')->references('id')->on('persons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salaries');
    }
}
