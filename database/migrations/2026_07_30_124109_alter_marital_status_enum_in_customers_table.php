<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterMaritalStatusEnumInCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            ALTER TABLE customers
            MODIFY marital_status ENUM(
                'single',
                'married',
                'widow',
                'widower',
                'separated',
                'divorced'
            ) NULL
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
            ALTER TABLE customers
            MODIFY marital_status ENUM(
                'single',
                'married',
                'widow',
                'widower',
                'seperated',
                'divorced'
            ) NULL
        ");
    }
}
