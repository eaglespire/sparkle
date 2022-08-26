<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('business_id');
            $table->string('name');
            $table->enum('kyc_status', ['PENDING','PROCESSING','RESUBMIT', 'DECLINED', 'APPROVED'])->nullable();
            $table->string('gender')->nullable();
            $table->string('b_day')->nullable();
            $table->string('b_month')->nullable();
            $table->string('b_year')->nullable();
            $table->string('line_1')->nullable();
            $table->string('line_2')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('proof_of_address')->nullable();
            $table->string('doc_type')->nullable();
            $table->string('document')->nullable();
            $table->timestamps();
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->uuid('receiver_id')->change();
        });
        Schema::table('businesses', function (Blueprint $table) {
            $table->uuid('id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('businesses');
    }
}
