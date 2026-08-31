<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermanentAddressFieldsToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->tinyInteger('is_address_permanent')->default(1)->after('state');
            $table->text('permanent_address')->nullable()->after('is_address_permanent');
            $table->string('permanent_pin_code')->nullable()->after('permanent_address');
            $table->string('permanent_city')->nullable()->after('permanent_pin_code',10);
            $table->string('permanent_state')->nullable()->after('permanent_city');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['is_address_permanent', 'permanent_address', 'permanent_pin_code', 'permanent_city', 'permanent_state']);
        });
    }
}
