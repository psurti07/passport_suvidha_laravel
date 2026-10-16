<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOfferTypeCommentInOfferOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_orders', function (Blueprint $table) {
            $table->string('offer_type', 191)->nullable()->comment('1=Card Offer, 2=Star Offer, 3=Bumper Offer')->change();
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
            Schema::table('offer_orders', function (Blueprint $table) {
                $table->string('offer_type', 191)->nullable()->comment('1=Card Offer, 2=Star Offer')->change();
            });
        });
    }
}
