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
        Schema::create('application_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('application_status');
            $table->dateTime('status_date');
            $table->text('remark')->nullable();
            $table->foreignId('remarked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('file_type', ['final_details', 'appointment_letters'])->nullable();
            $table->unsignedBigInteger('file')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_progress');
    }
}; 