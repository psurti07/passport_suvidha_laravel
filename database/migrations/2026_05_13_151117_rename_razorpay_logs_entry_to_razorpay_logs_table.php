<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameRazorpayLogsEntryToRazorpayLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('razorpay_logs', function (Blueprint $table) {
            Schema::rename('razorpay_logs_entry', 'razorpay_logs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('razorpay_logs', function (Blueprint $table) {
            Schema::rename('razorpay_logs', 'razorpay_logs_entry');
        });
    }
}
