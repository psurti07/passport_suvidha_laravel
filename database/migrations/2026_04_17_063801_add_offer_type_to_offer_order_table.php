<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfferTypeToOfferOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_order', function (Blueprint $table) {
            $table->string('offer_type')->nullable()->after('payment_id')->comment('1=Card Offer, 2=Star Offer');
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
            $table->dropColumn('offer_type');
        });
    }
}
