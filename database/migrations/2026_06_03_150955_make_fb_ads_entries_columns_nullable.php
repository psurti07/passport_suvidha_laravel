<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeFbAdsEntriesColumnsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fb_ads_entries', function (Blueprint $table) {
            $table->longText('sent_data')->nullable()->change();
            $table->longText('received_data')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fb_ads_entries', function (Blueprint $table) {
            $table->longText('sent_data')->nullable(false)->change();
            $table->longText('received_data')->nullable(false)->change();
        });
    }
}
