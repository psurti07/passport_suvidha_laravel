<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ServiceSeeder::class,
            AdminUserSeeder::class,
            ApplicationStatusSeeder::class,
            // Add other seeders here if needed
        ]);
    }
} 