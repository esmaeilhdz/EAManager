<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('person_companies');
        Schema::create('person_companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->index()->nullable();
            $table->unsignedBigInteger('person_id')->index();
            $table->unsignedBigInteger('company_id')->index();
            $table->date('start_work_date')->index()->comment('تاریخ شروع به کار');
            $table->date('end_ work_date')->index()->nullable()->comment('تاریخ پایان کار');
            $table->unsignedInteger('suggest_salary')->comment('حقوق پیشنهادی');
            $table->unsignedInteger('daily_income')->index()->comment('درآمد روزانه');
            $table->string('position')->comment('سمت');
            $table->boolean('is_enable')->index()->default(1);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('person_id')->references('id')->on('persons');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('person_companies');
    }
}
