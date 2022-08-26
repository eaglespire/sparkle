<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAuditLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audit_logs', function (Blueprint $table) {
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
