<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveRollCountClothTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cloth_buys', function (Blueprint $table) {
            $table->dropIndex('cloth_buys_color_id_index');
            $table->dropColumn(['roll_count', 'color_id', 'metre']);
        });
        Schema::table('cloth_sells', function (Blueprint $table) {
            $table->dropColumn('roll_count');
        });
        Schema::table('cloth_warehouses', function (Blueprint $table) {
            $table->dropColumn('roll_count');
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
