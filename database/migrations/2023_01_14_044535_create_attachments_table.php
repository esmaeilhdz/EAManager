<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('attachments');
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->unsignedTinyInteger('attachment_type_id')->index();
            $table->string('path');
            $table->string('file_name', 32);
            $table->string('original_file_name');
            $table->string('ext', 4);
            $table->string('type', 10);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachments');
    }
}
