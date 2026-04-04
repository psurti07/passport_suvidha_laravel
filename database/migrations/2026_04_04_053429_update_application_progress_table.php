<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateApplicationProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('application_progress', function (Blueprint $table) {
            $table->foreignId('status_id')->after('customer_id')->constrained('application_statuses')->cascadeOnDelete();
            $table->dropColumn('application_status');
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
            $table->string('application_status');
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
        });
    }
}
