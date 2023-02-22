<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatGroupPersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('chat_group_persons');
        Schema::create('chat_group_persons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_group_id');
            $table->unsignedBigInteger('person_id');
            $table->timestamps();

            $table->engine = 'InnoDB';

            $table->foreign('chat_group_id')->references('id')->on('chat_groups');
            $table->foreign('person_id')->references('id')->on('people');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_group_persons');
    }
}
