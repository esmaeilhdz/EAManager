<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('product_prices');
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('total_count')->comment('تعداد کل');
            $table->unsignedInteger('serial_count')->comment('تعداد سری');
            $table->unsignedInteger('sewing_price')->comment('اجرت دوخت');
            $table->unsignedInteger('cutting_price')->comment('اجرت برش');
            $table->unsignedInteger('sewing_final_price')->comment('قیمت تمام شده دوخت');
            $table->unsignedInteger('sale_profit_price')->comment('سود فروش');
            $table->unsignedInteger('final_price')->comment('قیمت نهایی (قیمت فروشگاه)');
            $table->boolean('is_enable')->default(1)->index();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->engine = 'InnoDB';

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
        Schema::dropIfExists('product_prices');
    }
}
