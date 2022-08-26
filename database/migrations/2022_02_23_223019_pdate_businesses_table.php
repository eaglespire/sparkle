<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PdateBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('industry');
            $table->integer('type')->nullable();
            $table->string('category')->nullable();
            $table->string('staff_size')->nullable();
            $table->string('legal_name')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('reg_no')->nullable();
            $table->string('vat_id')->nullable();
            $table->string('registration_type')->nullable();
            $table->string('business_document')->nullable();
            $table->string('business_line_1')->nullable();
            $table->string('business_line_2')->nullable();
            $table->string('business_state')->nullable();
            $table->string('business_city')->nullable();
            $table->string('business_postal_code')->nullable();
            $table->string('business_proof_of_address')->nullable();
        });
        Schema::table('balance', function (Blueprint $table) {
            $table->string('business_id', 10);
        });
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->string('business_id', 10);
        });        
        Schema::table('ext_transfer', function (Blueprint $table) {
            $table->string('business_id', 10);
        });  
        Schema::table('payment_link', function (Blueprint $table) {
            $table->string('business_id', 10);
        });        
        Schema::table('virtual_cards', function (Blueprint $table) {
            $table->string('business_id', 10);
        });
        Schema::table('virtual_transactions', function (Blueprint $table) {
            $table->string('business_id', 10);
        });
        Schema::table('reply_support', function (Blueprint $table) {
            $table->string('business_id', 10);
        });
        Schema::table('support', function (Blueprint $table) {
            $table->string('business_id', 10);
        });  
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('business_id', 10);
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
