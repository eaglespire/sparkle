<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('routing_no')->nullable();
            $table->string('iban')->nullable();
            $table->string('acct_no')->nullable();
            $table->string('sort_code')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('acct_name')->nullable();
            $table->integer('country');
            $table->string('name');
            $table->timestamps();
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('name');
        });           
        Schema::table('users', function (Blueprint $table) {
            $table->integer('email_receiver')->default(1);
            $table->integer('email_sender')->default(1);
        });   
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beneficiaries');
    }
}
