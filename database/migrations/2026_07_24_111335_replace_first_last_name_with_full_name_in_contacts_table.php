<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ReplaceFirstLastNameWithFullNameInContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('full_name')->after('id')->nullable();
        });

        DB::statement("
            UPDATE contacts
            SET full_name = TRIM(CONCAT(IFNULL(first_name,''), ' ', IFNULL(last_name,'')))
        ");

        Schema::table('contacts', function (Blueprint $table) {
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
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('full_name');
        });
    }
}
