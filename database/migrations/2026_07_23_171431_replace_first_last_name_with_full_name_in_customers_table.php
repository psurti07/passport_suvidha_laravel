<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ReplaceFirstLastNameWithFullNameInCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('full_name')->after('service_id')->nullable();
        });

        DB::statement("
            UPDATE customers
            SET full_name = TRIM(CONCAT(IFNULL(first_name,''), ' ', IFNULL(last_name,'')))
        ");

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
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
            $table->string('first_name')->nullable()->after('service_id');
            $table->string('last_name')->nullable()->after('first_name');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('full_name');
        });
    }
}
