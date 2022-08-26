<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('balance', function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->uuid('user_id')->change();
        });
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->uuid('user_id')->change();
        });        
        Schema::table('card_errors', function (Blueprint $table) {
            $table->uuid('id')->change();
        });
        Schema::table('ext_transfer', function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->uuid('user_id')->change();
        });  
        Schema::table('password_resets', function (Blueprint $table) {
            $table->uuid('id')->change();
        }); 
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('id')->change();
        }); 
        Schema::table('payment_link', function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->uuid('user_id')->change();
        });        
        Schema::table('virtual_cards', function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->uuid('user_id')->change();
        });
        Schema::table('virtual_transactions', function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->uuid('user_id')->change();
            $table->timestamp('updated_at')->nullable()->default(NULL)->change();
            $table->timestamp('created_at')->nullable()->change();
        });
        Schema::table('reply_support', function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->timestamp('updated_at')->nullable()->default(NULL)->change();
            $table->timestamp('created_at')->nullable()->change();
        });
        Schema::table('support', function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->uuid('user_id')->change();
            $table->timestamp('updated_at')->nullable()->default(NULL)->change();
            $table->timestamp('created_at')->nullable()->change();
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
