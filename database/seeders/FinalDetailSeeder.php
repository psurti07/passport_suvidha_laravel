<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\FinalDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class FinalDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure we have users
        $admin = User::where('email', 'admin@example.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }

        $staff = User::where('email', 'staff@example.com')->first();
        if (!$staff) {
            $staff = User::create([
                'name' => 'Staff User',
                'email' => 'staff@example.com',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]);
        }

        // Create or find customers
        $customers = Customer::take(3)->get();
        if ($customers->count() < 3) {
            // Create customers manually if needed
            for ($i = 0; $i < 3 - $customers->count(); $i++) {
                $customer = Customer::create([
                    'first_name' => 'Customer',
                    'last_name' => 'Test ' . ($i + 1),
                    'email' => 'customer' . ($i + 1) . '@example.com',
                    'mobile_number' => '98765' . rand(10000, 99999),
                ]);
                $customers->push($customer);
            }
        }

        // Create sample final details
        foreach ($customers as $index => $customer) {
            // Create approved final detail by admin
            FinalDetail::create([
                'customer_id' => $customer->id,
                'file_path' => 'final-details/sample-pdf-' . $index . '.pdf',
                'upload_date' => now()->subDays($index + 1),
                'uploaded_by' => $admin->id,
                'is_approved' => true,
                'approved_date' => now()->subDays($index),
                'approved_by_role' => 'user',
                'approved_by' => $admin->id,
            ]);

            // Create unapproved final detail by staff
            FinalDetail::create([
                'customer_id' => $customer->id,
                'file_path' => 'final-details/sample-image-' . $index . '.jpg',
                'upload_date' => now()->subHours($index + 1),
                'uploaded_by' => $staff->id,
                'is_approved' => false,
            ]);
        }

        // Ensure a folder exists for test files
        Storage::disk('public')->makeDirectory('final-details');

        // Create dummy PDF files
        foreach (range(0, 2) as $index) {
            $content = "This is a sample PDF file for testing purposes (#$index).\n";
            $content .= "Customer: " . $customers[$index]->full_name . "\n";
            $content .= "Timestamp: " . now()->toString();
            
            Storage::disk('public')->put("final-details/sample-pdf-$index.pdf", $content);
        }

        // Create dummy image files (text files with .jpg extension for testing)
        foreach (range(0, 2) as $index) {
            $content = "This is a placeholder for a sample image file for testing purposes.";
            Storage::disk('public')->put("final-details/sample-image-$index.jpg", $content);
        }

        $this->command->info('Created ' . (count($customers) * 2) . ' final details with sample files.');
    }
}
