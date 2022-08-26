<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNameTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('pending')->default(0);
        });
        Schema::table('country_supported', function (Blueprint $table) {
            $table->integer('pending_balance_duration')->default(0);
        });
        Schema::table('balance', function (Blueprint $table) {
            $table->integer('pending')->default(0);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->integer('business_id')->nullable();
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
