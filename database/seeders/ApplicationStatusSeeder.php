<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApplicationStatus;

class ApplicationStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            ['status_name' => 'In Process', 'slug' => 'in_process', 'priority_no' => 1],
            ['status_name' => 'Documents Submitted', 'slug' => 'documents_submitted', 'priority_no' => 2],
            ['status_name' => 'Details Verification', 'slug' => 'details_verification', 'priority_no' => 3],
            ['status_name' => 'Appointment Scheduled', 'slug' => 'appointment_scheduled', 'priority_no' => 4],
            ['status_name' => 'POV Success', 'slug' => 'pov_success', 'priority_no' => 5],
            ['status_name' => 'POV Failed', 'slug' => 'pov_failed', 'priority_no' => 6],
            ['status_name' => 'POV Insufficient Documents', 'slug' => 'pov_insufficient_documents', 'priority_no' => 7],
            ['status_name' => 'Appointment Rescheduled 1', 'slug' => 'appointment_rescheduled1', 'priority_no' => 8],
            ['status_name' => 'Appointment Rescheduled 2', 'slug' => 'appointment_rescheduled2', 'priority_no' => 9],
            ['status_name' => 'Appointment Rescheduled 3', 'slug' => 'appointment_rescheduled3', 'priority_no' => 10],
        ];

        foreach ($statuses as $status) {
            ApplicationStatus::updateOrCreate(
                ['slug' => $status['slug']],
                $status
            );
        }
    }
}
