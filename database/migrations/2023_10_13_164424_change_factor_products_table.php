<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFactorProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('factor_products', 'factor_items');
        Schema::table('factor_items', function (Blueprint $table) {
            $table->morphs('model');
            $table->dropForeign('factor_products_product_warehouse_id_foreign');
            $table->dropColumn('product_warehouse_id');

            $table->unsignedInteger('free_size_count')->nullable()->change();
            $table->unsignedInteger('size1_count')->nullable()->change();
            $table->unsignedInteger('size2_count')->nullable()->change();
            $table->unsignedInteger('size3_count')->nullable()->change();
            $table->unsignedInteger('size4_count')->nullable()->change();

            $table->unsignedInteger('metre')->nullable()->after('size4_count');
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
