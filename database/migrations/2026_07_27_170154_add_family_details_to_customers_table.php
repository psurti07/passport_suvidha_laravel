<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFamilyDetailsToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('father_name')->nullable()->after('email');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->enum('marital_status', [
                'single',
                'married',
                'widow',
                'widower',
                'seperated',
                'divorced'
            ])->nullable()->after('mother_name');
            $table->string('spouse_name')->nullable()->after('marital_status'); 
            $table->string('emergency_contact_name')->nullable()->after('spouse_name');
            $table->string('emergency_contact_mobile', 20)->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_email')->nullable()->after('emergency_contact_mobile');
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
            $table->dropColumn([
                'father_name',
                'mother_name',
                'marital_status',
                'spouse_name',
                'emergency_contact_name',
                'emergency_contact_mobile',
                'emergency_contact_email',
            ]);
        });
    }
}
