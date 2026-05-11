<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeleteColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            'application_documents',
            'application_orders',
            'appointment_letters',
            'final_details',
            'tickets',
            'ticket_remarks',
            'users',
            'pre_defined_messages'
        ];

        foreach ($tables as $tableName) {

            Schema::table($tableName, function (Blueprint $table) {

                $table->softDeletes();

            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
