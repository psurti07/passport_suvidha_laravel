<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCustomerToOfferOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_order', function (Blueprint $table) {
            $table->tinyInteger('is_customer')
              ->default(0)
              ->comment('0 = Lead, 1 = Customer')
              ->after('offer_type');
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
            $table->dropColumn('is_customer');
        });
    }
}
