<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAccessoryBuysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accessory_buys', function (Blueprint $table) {
            $table->date('receive_date')->after('count');
            $table->string('factor_no')->after('receive_date');
            $table->string('unit_price')->after('factor_no');
            $table->string('price')->after('unit_price');
            $table->text('description')->after('price');
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
