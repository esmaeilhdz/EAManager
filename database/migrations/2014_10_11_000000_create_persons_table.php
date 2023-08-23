<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('people');
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->index()->unique();
            $table->string('internal_code', 16)->unique()->comment('کد سیستمی - کد پرسنلی');
            $table->string('name', 60)->index();
            $table->string('family', 70)->index();
            $table->string('father_name', 60);
            $table->string('national_code', 10)->index()->unique()->comment('کدملی');
            $table->string('identity', 15)->comment('شماره شناسنامه');
            $table->string('passport_no', 20)->index()->nullable()->comment('شماره پاسپورت');
            $table->unsignedInteger('score')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->softDeletes();
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
        Schema::dropIfExists('people');
    }
}
