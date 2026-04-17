<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeOrderAmountInRazorpayLogsEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('razorpay_logs_entry', function (Blueprint $table) {
            $table->decimal('order_amount', 8, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('razorpay_logs_entry', function (Blueprint $table) {
            $table->integer('order_amount')->change();
        });
    }
}
