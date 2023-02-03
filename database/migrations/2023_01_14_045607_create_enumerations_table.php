<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnumerationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('enumerations');
        Schema::create('enumerations', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->string('category_caption');
            $table->string('enum_caption');
            $table->unsignedTinyInteger('enum_id');
            $table->boolean('is_enable')->default(1);
            $table->boolean('is_editable');
            $table->unsignedBigInteger('created_by');

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
        Schema::dropIfExists('enumerations');
    }
}
