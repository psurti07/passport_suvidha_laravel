<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayuLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payu_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('order_id')->nullable();
            $table->decimal('order_amount', 8, 2);
            $table->string('order_note')->nullable();
            $table->string('reference_id', 255)->nullable();
            $table->string('payment_id')->nullable();
            $table->string('tx_status')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('service_type', 20)->nullable();
            $table->unsignedTinyInteger('offer_type')->nullable()->comment('1=Card Offer, 2=Star Offer, 3=Bumper Offer');
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
        Schema::dropIfExists('payu_logs');
    }
}
