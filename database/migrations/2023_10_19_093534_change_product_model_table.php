<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeProductModelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_models', function (Blueprint $table) {
            $table->unsignedTinyInteger('pack_count')->after('name')->comment('تعداد در بسته');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_models', function (Blueprint $table) {
            $table->dropColumn('pack_count');
        });
    }
}
