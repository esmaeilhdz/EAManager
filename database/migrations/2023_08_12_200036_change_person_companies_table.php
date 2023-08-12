<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangePersonCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('person_companies', function (Blueprint $table) {
            DB::statement("ALTER TABLE person_companies MODIFY suggest_salary integer unsigned NULL;");
            DB::statement("ALTER TABLE person_companies MODIFY daily_income integer unsigned NULL;");
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
