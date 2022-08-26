<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'public_key', 
                'secret_key', 
                'test_secret_key', 
                'test_public_key', 
                'live', 
                'kyc_status', 
                'gender', 
                'b_day', 
                'b_month', 
                'b_year', 
                'line_1', 
                'line_2', 
                'state', 
                'city', 
                'postal_code', 
                'proof_of_address', 
                'doc_type', 
                'document', 
                'webhook', 
                'receive_webhook', 
                'charges', 
                'card', 
                'bank_account', 
                'mobile_money', 
                'email_receiver', 
                'email_sender', 
                'webhook_secret', 
            ]);
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
