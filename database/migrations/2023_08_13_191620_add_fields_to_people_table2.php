<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToPeopleTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->string('card_no', 16)->after('passport_no');
            $table->string('bank_account_no')->nullable()->after('card_no');
            $table->string('sheba_no', 24)->nullable()->after('bank_account_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn(['card_no', 'bank_account_no', 'sheba_no']);
        });
    }
}
