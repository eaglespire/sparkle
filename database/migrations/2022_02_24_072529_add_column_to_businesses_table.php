<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->integer('email_receiver')->default(1);
            $table->integer('email_sender')->default(1);
            $table->integer('live')->default(0);
            $table->integer('charges')->default(0);
            $table->string('public_key')->nullable();
            $table->string('secret_key')->nullable();
            $table->string('test_public_key')->nullable();
            $table->string('test_secret_key')->nullable();
            $table->string('webhook')->nullable();
            $table->integer('receive_webhook')->nullable();
            $table->integer('card')->default(0);
            $table->integer('bank_account')->default(0);
            $table->integer('mobile_money')->default(0);
            $table->string('webhook_secret')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
            //
        });
    }
}
