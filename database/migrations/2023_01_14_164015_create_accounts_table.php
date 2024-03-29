<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('cheques');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('account_cheques');
        Schema::dropIfExists('accounts');
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique()->index();
            $table->unsignedBigInteger('company_id');
            $table->unsignedTinyInteger('bank_id')->index();
            $table->string('branch_name')->nullable()->index()->comment(' نام شعبه بانک');
            $table->string('account_no', 100);
            $table->string('sheba_no', 30);
            $table->string('card_no', 20);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->engine = 'InnoDB';

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
        Schema::dropIfExists('accounts');
    }
}
