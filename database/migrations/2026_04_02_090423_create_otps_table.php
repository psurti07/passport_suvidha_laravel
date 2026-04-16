<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('mobile_number');
            $table->string('otp');
            $table->timestamp('sent_at')->useCurrent();
            $table->boolean('is_verified')->default(false);
            $table->unsignedInteger('attempts')->default(0);
            $table->boolean('is_blocked')->default(false);
            $table->string('purpose')->default('registration');
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
        Schema::dropIfExists('otps');
    }
}
