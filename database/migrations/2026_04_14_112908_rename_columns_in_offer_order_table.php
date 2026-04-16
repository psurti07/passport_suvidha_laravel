<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnsInOfferOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_order', function (Blueprint $table) {
            $table->renameColumn('fullname', 'full_name');
            $table->renameColumn('paymentid', 'payment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offer_order', function (Blueprint $table) {
            $table->renameColumn('full_name', 'fullname');
            $table->renameColumn('payment_id', 'paymentid');
        });
    }
}
