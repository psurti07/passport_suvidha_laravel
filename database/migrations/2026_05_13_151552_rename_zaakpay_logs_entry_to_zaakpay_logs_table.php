<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameZaakpayLogsEntryToZaakpayLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zaakpay_logs', function (Blueprint $table) {
            Schema::rename('zaakpay_logs_entry', 'zaakpay_logs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zaakpay_logs', function (Blueprint $table) {
            Schema::rename('zaakpay_logs', 'zaakpay_logs_entry');
        });
    }
}
