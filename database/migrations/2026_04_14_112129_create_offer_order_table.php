<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_order', function (Blueprint $table) {
            $table->id();
            $table->string('fullname', 256);
            $table->string('mobile', 256);
            $table->string('email', 256);
            $table->string('card_number', 256)->nullable();
            $table->float('amount', 11, 2);
            $table->string('paymentid', 50)->nullable();
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
        Schema::dropIfExists('offer_order');
    }
}
