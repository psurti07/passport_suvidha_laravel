<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRazorpayLogsEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('razorpay_logs_entry', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->unsignedBigInteger('order_id');
            $table->integer('order_amount');
            $table->string('order_note')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('tx_status')->nullable();
            $table->string('payment_mode')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('razorpay_logs_entry');
    }
}
