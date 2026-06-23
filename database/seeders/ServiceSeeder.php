<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing services
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Service::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Normal Passport (36 pages)
        Service::create([
            'service_name' => 'Normal Passport (36 pages)',
            'service_code' => 'NP36',
            'service_gov_amount' => 1500,
            'service_charges' => 999,
            'service_gst' => 179.82,
            'service_total_amount' => 1178.82,
        ]);

        // Normal Passport (60 pages)
        Service::create([
            'service_name' => 'Normal Passport (60 pages)',
            'service_code' => 'NP60',
            'service_gov_amount' => 2000,
            'service_charges' => 999,
            'service_gst' => 179.82,
            'service_total_amount' => 1178.82,
        ]);

        // Tatkal Passport (36 pages)
        Service::create([
            'service_name' => 'Tatkal Passport (36 pages)',
            'service_code' => 'TP36',
            'service_gov_amount' => 3500,
            'service_charges' => 999,
            'service_gst' => 179.82,
            'service_total_amount' => 1178.82,
        ]);

        // Tatkal Passport (60 pages)
        Service::create([
            'service_name' => 'Tatkal Passport (60 pages)',
            'service_code' => 'TP60',
            'service_gov_amount' => 4000,
            'service_charges' => 999,
            'service_gst' => 179.82,
            'service_total_amount' => 1178.82,
        ]);
    }
}
