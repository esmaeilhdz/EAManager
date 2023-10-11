<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeClothTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cloths', function (Blueprint $table) {
            $table->dropIndex('cloths_color_id_index');
            $table->dropColumn('color_id');
        });

        Schema::table('cloth_buys', function (Blueprint $table) {
            $table->unsignedInteger('color_id')->index()->after('cloth_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cloths', function (Blueprint $table) {
            $table->unsignedInteger('cloth_id')->index()->after('cloth_id');
        });

        Schema::table('cloth_buys', function (Blueprint $table) {
            $table->dropIndex('cloth_buys_color_id_index');
            $table->dropColumn('color_id');
        });
    }
}
