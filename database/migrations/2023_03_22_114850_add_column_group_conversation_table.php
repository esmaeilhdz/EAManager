<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnGroupConversationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_conversations', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->after('id');
            $table->boolean('is_enable')->default(1)->after('name');
            $table->unsignedBigInteger('created_by')->after('is_enable');

            $table->foreign('company_id')->references('id')->on('companies');

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
