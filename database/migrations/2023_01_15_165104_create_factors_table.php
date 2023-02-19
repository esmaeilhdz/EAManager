<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFactorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('factors');
        Schema::create('factors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('sale_period_id');
            $table->string('factor_no', 16)->unique();
            $table->boolean('has_return_permission')->default(0)->comment('اجازه مرجوع');
            $table->boolean('is_credit')->default(0)->comment('فروش اعتباری(امانی)');
            $table->date('settlement_date')->nullable()->comment('تاریخ تسویه');
            $table->unsignedTinyInteger('status')->default(1)->index()->comment('وضعیت فاکتور');
            $table->unsignedInteger('final_price');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->dateTime('returned_at')->nullable()->comment('تاریخ مرجوعی');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('sale_period_id')->references('id')->on('sale_periods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('factors');
    }
}
