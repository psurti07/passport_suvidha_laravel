<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddStatusToFinalDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('final_details', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'approved',
                'not_approved'
            ])->default('pending')->after('is_approved');
        });

        DB::table('final_details')
            ->where('is_approved', true)
            ->update(['status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('final_details', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
