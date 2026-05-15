<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDeletedAtFromMultipleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('appointment_letters', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('final_details', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('pre_defined_messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('appointment_letters', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('final_details', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('pre_defined_messages', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
}
