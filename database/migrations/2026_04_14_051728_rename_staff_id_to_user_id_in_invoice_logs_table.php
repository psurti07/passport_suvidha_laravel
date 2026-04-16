<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameStaffIdToUserIdInInvoiceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_logs', function (Blueprint $table) {
            $table->renameColumn('staff_id', 'user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_logs', function (Blueprint $table) {
            $table->renameColumn('user_id', 'staff_id');
        });
    }
}
