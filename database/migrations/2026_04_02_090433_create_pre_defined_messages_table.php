<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreDefinedMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_defined_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('status_id')->constrained('application_statuses')->cascadeOnDelete();
            $table->string('message_name')->unique();
            $table->text('message_remarks');
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
        Schema::dropIfExists('pre_defined_messages');
    }
}
