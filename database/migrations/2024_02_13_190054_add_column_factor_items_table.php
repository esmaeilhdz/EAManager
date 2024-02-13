<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFactorItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::table('factor_items', function (Blueprint $table) {
            $table->unsignedTinyInteger('discount_type_id')->nullable()->after('price')->comment('نوع تخفیف');
            $table->unsignedInteger('discount')->nullable()->after('discount_type_id')->comment('مقدار تخفیف');
        });*/

        \App\Models\Enumeration::insert([
            [
                'category_name' => 'discount_type',
                'category_caption' => 'نوع تخفیف',
                'enum_caption' => 'درصد',
                'enum_id' => 1,
                'is_enable' => 1,
                'is_editable' => 0,
            ],
            [
                'category_name' => 'discount_type',
                'category_caption' => 'نوع تخفیف',
                'enum_caption' => 'ریال',
                'enum_id' => 2,
                'is_enable' => 1,
                'is_editable' => 0,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('factor_items', function (Blueprint $table) {
            $table->dropColumn(['discount_type_id', 'discount']);
        });
    }
}
