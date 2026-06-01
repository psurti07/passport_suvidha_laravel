<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeTypePositionInSmsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            ALTER TABLE sms_logs
            MODIFY COLUMN type VARCHAR(255)
            NULL
            AFTER id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
            ALTER TABLE sms_logs
            MODIFY COLUMN type VARCHAR(255)
            NULL
            AFTER crontype
        ");
    }
}
