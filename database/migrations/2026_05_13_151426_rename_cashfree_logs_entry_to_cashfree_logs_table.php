<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCashfreeLogsEntryToCashfreeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashfree_logs', function (Blueprint $table) {
            schema::rename('cashfree_logs_entry', 'cashfree_logs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashfree_logs', function (Blueprint $table) {
            schema::rename('cashfree_logs', 'cashfree_logs_entry');
        });
    }
}
