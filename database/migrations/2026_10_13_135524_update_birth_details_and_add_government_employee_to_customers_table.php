<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBirthDetailsAndAddGovernmentEmployeeToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('place_of_birth', 'birth_city');
            $table->string('birth_state')->nullable()->after('birth_city');
            $table->string('birth_district')->nullable()->after('birth_state');
            $table->boolean('is_ps_gov_emp')->default(0)->after('birth_district');
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
            $table->dropColumn(['birth_state', 'birth_district', 'is_ps_gov_emp',]);
            $table->renameColumn('birth_city', 'place_of_birth');
        });
    }
}
