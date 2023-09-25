<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeProductPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_prices', function (Blueprint $table) {
            /*$table->unsignedBigInteger('cutter_person_id')->nullable()->after('product_id')->comment('دوزنده داخلی');
            $table->unsignedBigInteger('cutter_place_id')->nullable()->after('cutter_person_id')->comment('دوزنده خارجی');
            $table->date('cutting_date')->after('cutting_price')->comment('تاریخ برش');
            $table->date('sewing_date')->after('sewing_price')->comment('تاریخ دوخت');
            $table->unsignedInteger('packing_price')->after('sewing_final_price')->comment('اجرت بسته بندی');
            $table->unsignedInteger('sending_price')->after('packing_price')->comment('اجرت ارسال');

            $table->foreign('cutter_person_id')->references('id')->on('people');*/
            $table->foreign('cutter_place_id')->references('id')->on('places');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
