<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDatesFromApplicationOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('application_orders', function (Blueprint $table) {
            $table->dropColumn('registration_date');
            $table->dropColumn('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('application_orders', function (Blueprint $table) {
            $table->date('registration_date')->nullable();
            $table->date('expiry_date')->nullable();
        });
    }
}
