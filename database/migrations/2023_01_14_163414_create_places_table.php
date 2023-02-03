<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('places');
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('place_kind_id')->index()->comment('نوع مکان');
            $table->string('department_manager_name')->nullable()->comment('نام مسئول');
            $table->string('department_manager_national_code', 11)->nullable()->comment('کدملی مسئول');
            $table->string('department_manager_identity', 11)->nullable()->comment('شماره شناسنامه مسئول');
            $table->unsignedTinyInteger('capacity')->nullable();
            $table->date('from_date')->comment('تاریخ شروع همکاری');
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
        Schema::dropIfExists('places');
    }
}
