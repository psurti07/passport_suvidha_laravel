<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceTypeAndOfferTypeToCashfreeLogsEntry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashfree_logs_entry', function (Blueprint $table) {
            $table->string('service_type', 20)
                  ->nullable()
                  ->after('payment_mode')
                  ->comment('NP36, NP60, TP36, TP60');

            $table->tinyInteger('offer_type')
                  ->nullable()
                  ->after('service_type')
                  ->comment('1=Card Offer, 2=Star Offer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashfree_logs_entry', function (Blueprint $table) {
            $table->dropColumn(['service_type', 'offer_type']);
        });
    }
}
