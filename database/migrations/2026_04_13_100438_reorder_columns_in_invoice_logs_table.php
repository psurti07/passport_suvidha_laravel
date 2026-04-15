<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReorderColumnsInInvoiceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_logs', function (Blueprint $table) {
            try {
                $table->dropForeign(['invoice_id']);
                $table->dropForeign(['staff_id']);
            } catch (\Exception $e) {}
            $table->dropColumn(['invoice_id', 'staff_id']);
        });

        Schema::table('invoice_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->after('id');
            $table->unsignedBigInteger('staff_id')->after('invoice_id');

            $table->foreign('invoice_id')
                  ->references('id')
                  ->on('invoices')
                  ->onDelete('cascade');

            $table->foreign('staff_id')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnDelete('cascade');
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
            $table->unsignedBigInteger('invoice_id')
                  ->after('card_number')
                  ->change();

            $table->unsignedBigInteger('staff_id')
                  ->after('invoice_id')
                  ->change();
        });
    }
}
