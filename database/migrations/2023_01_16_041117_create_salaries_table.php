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
        Schema::dropIfExists('salary_deductions');
        Schema::dropIfExists('salaries');
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('person_id');
            $table->date('from_date');
            $table->date('to_date');
            $table->unsignedInteger('reward_price')->comment('پاداش');
            $table->unsignedTinyInteger('overtime_hour')->comment('ساعت اضافه کار');
            $table->boolean('is_checkout')->default(0)->index()->comment('تسویه حساب شده/نشده');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('person_id')->references('id')->on('people');
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
