<?php

namespace Database\Seeders;

use App\Models\Customer; // Assuming your model is App\Models\Customer
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB facade if needed, though Eloquent is used here

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'mobile_number' => '9876543210',
            'email' => 'john.doe@example.com',
            'address' => '123 Main St, Anytown',
            'gender' => 'male',
            'date_of_birth' => '1990-05-15',
            'place_of_birth' => 'Anytown',
            'nationality' => 'Example Nation',
            'service_code' => 'PASSPORT-001',
            'is_paid' => true,
        ]);

        Customer::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'mobile_number' => '9876543211',
            'email' => 'jane.smith@example.com',
            'address' => '456 Oak Ave, Othertown',
            'gender' => 'female',
            'date_of_birth' => '1992-08-22',
            'place_of_birth' => 'Othertown',
            'nationality' => 'Example Nation',
            'service_code' => 'VISA-002',
            'is_paid' => false,
        ]);

        // Add more customer records as needed
    }
}
