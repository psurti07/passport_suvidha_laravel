<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileFieldsToApplicationProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('application_progress', function (Blueprint $table) {
            $table->enum('file_type', ['final_details', 'appointment_letters'])->nullable()->after('remarked_by');
            $table->unsignedBigInteger('file')->nullable()->after('file_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('application_progress', function (Blueprint $table) {
            $table->dropColumn(['file_type', 'file']);
        });
    }
}
