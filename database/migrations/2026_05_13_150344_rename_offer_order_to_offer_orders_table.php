<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameOfferOrderToOfferOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_orders', function (Blueprint $table) {
            Schema::rename('offer_order', 'offer_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offer_orders', function (Blueprint $table) {
            Schema::rename('offer_orders', 'offer_order');
        });
    }
}
