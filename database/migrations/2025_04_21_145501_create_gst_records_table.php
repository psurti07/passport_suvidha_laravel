<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gst_records', function (Blueprint $table) {
            $table->id();
            $table->date('inv_date')->nullable();
            $table->string('inv_no')->index()->nullable();
            $table->decimal('net_amount', 10, 2)->default(0.00);
            $table->decimal('cgst', 10, 2)->default(0.00);
            $table->decimal('sgst', 10, 2)->default(0.00);
            $table->decimal('igst', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->string('fullname')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('gst_no')->nullable()->index();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
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
        Schema::dropIfExists('gst_records');
    }
};
