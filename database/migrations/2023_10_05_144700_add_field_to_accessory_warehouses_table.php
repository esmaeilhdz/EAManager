<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToAccessoryWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accessory_warehouses', function (Blueprint $table) {
            $table->unsignedBigInteger('place_id')->after('accessory_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accessory_warehouses', function (Blueprint $table) {
            $table->dropColumn('place_id');
        });
    }
}
