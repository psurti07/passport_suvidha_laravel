<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRazorpayLogsEntry extends Migration
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

            $table->timestamp('rec_date')->useCurrent();

            $table->integer('entryfor')->nullable();

            $table->integer('userid');

            $table->integer('orderid');

            $table->integer('orderamount');

            $table->string('ordernote')->nullable();

            $table->string('referenceid')->nullable();

            $table->string('txstatus')->nullable();

            $table->string('paymentmode')->nullable();
            
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
