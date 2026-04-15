<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_remarks', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number');
            $table->unsignedBigInteger('user_id');
            $table->text('comment');
            $table->timestamps(0);

            // Add foreign key constraint for ticket_number
            $table->foreign('ticket_number')->references('ticket_number')->on('tickets')->onDelete('cascade');

            // Add foreign key constraints if needed, assuming 'users' and 'tickets' tables exist
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('ticket_number')->references('ticket_number')->on('tickets')->onDelete('cascade'); // Note: Foreign key on non-primary key
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_remarks');
    }
}
