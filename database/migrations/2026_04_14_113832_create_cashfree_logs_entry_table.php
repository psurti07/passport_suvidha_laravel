<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashfreeLogsEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashfree_logs_entry', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->bigInteger('order_id');
            $table->integer('order_amount');
            $table->string('order_note', 255)->nullable();
            $table->string('reference_id', 255)->nullable();
            $table->string('tx_status', 255)->nullable();
            $table->string('payment_mode', 255)->nullable();
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
        Schema::dropIfExists('cashfree_logs_entry');
    }
}
