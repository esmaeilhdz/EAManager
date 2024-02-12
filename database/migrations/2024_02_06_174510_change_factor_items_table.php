<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFactorItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('factor_items', function (Blueprint $table) {
            $table->dropColumn(['free_size_count', 'size1_count', 'size2_count', 'size3_count', 'size4_count']);

            $table->unsignedInteger('pack_count')->nullable()->after('model_id');
            $table->unsignedInteger('count')->nullable()->after('pack_count');
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
