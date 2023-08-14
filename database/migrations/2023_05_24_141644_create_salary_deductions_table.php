<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryDeductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('salary_deductions');
        Schema::create('salary_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salary_id')->index();
            $table->unsignedBigInteger('product_id')->index()->nullable();
            $table->unsignedInteger('price');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('salary_id')->references('id')->on('salaries');
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
        Schema::dropIfExists('salary_deductions');
    }
}
