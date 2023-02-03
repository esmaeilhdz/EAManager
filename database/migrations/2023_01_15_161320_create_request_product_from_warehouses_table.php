<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestProductFromWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('request_product_from_warehouses');
        Schema::create('request_product_from_warehouses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_warehouse_id');
            $table->unsignedBigInteger('request_user_id')->comment('کاربر درخواست کننده');
            $table->unsignedBigInteger('confirm_user_id')->nullable()->comment('کاربر تایید/رد کننده');
            $table->unsignedInteger('free_size_count');
            $table->unsignedInteger('size1_count');
            $table->unsignedInteger('size2_count');
            $table->unsignedInteger('size3_count');
            $table->unsignedInteger('size4_count');
            $table->boolean('is_confirm')->default(0)->comment('وضعیت تایید درخواست');
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
        Schema::dropIfExists('request_product_from_warehouses');
    }
}
