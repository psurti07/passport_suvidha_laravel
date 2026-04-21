<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLogsAddPaymentIdAndMakeOrderNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('razorpay_logs_entry', function (Blueprint $table) {
            $table->string('order_id')->nullable()->change();
            $table->string('payment_id')->nullable()->after('reference_id');
        });

        Schema::table('cashfree_logs_entry', function (Blueprint $table) {
            $table->string('order_id')->nullable()->change();
            $table->string('payment_id')->nullable()->after('reference_id');
        });

        Schema::table('zaakpay_logs_entry', function (Blueprint $table) {
            $table->string('order_id')->nullable()->change();
            $table->string('payment_id')->nullable()->after('reference_id');
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
            $table->string('order_id')->nullable(false)->change();
            $table->dropColumn('payment_id');
        });

        Schema::table('cashfree_logs_entry', function (Blueprint $table) {
            $table->string('order_id')->nullable(false)->change();
            $table->dropColumn('payment_id');
        });

        Schema::table('zaakpay_logs_entry', function (Blueprint $table) {
            $table->string('order_id')->nullable(false)->change();
            $table->dropColumn('payment_id');
        });
    }
}
