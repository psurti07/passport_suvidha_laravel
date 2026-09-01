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
            ['status_name' => 'Not Contact 2 Days Warning', 'slug' => 'not_contact_2_days_warning', 'priority_no' => 1, 'colorclass' => 'yellow', 'step' => 7],

            ['status_name' => 'Not Contact 2 Days Reject', 'slug' => 'not_contact_2_days_reject', 'priority_no' => 2, 'colorclass' => 'red', 'step' => 8],

            ['status_name' => 'Payment Refund Update', 'slug' => 'refunded', 'priority_no' => 3, 'colorclass' => 'purple', 'step' => 9],

            ['status_name' => 'Verification Ohk', 'slug' => 'verification_ohk', 'priority_no' => 4, 'colorclass' => 'blue', 'step' => 10],

            ['status_name' => 'Documents Submitted', 'slug' => 'documents_submitted', 'priority_no' => 5, 'colorclass' => 'green', 'step' => 11],

            ['status_name' => 'Documents Pending (Warning)', 'slug' => 'documents_pending_warning', 'priority_no' => 6, 'colorclass' => 'yellow', 'step' => 12],

            ['status_name' => 'Documents Pending (Reject)', 'slug' => 'documents_pending_reject', 'priority_no' => 7, 'colorclass' => 'red', 'step' => 13],

            ['status_name' => 'Details Verification', 'slug' => 'details_verification', 'priority_no' => 8, 'colorclass' => 'green', 'step' => 14],

            ['status_name' => 'Details Verification Pending 3 Days Warning', 'slug' => 'details_verification_pending_3_days_warning', 'priority_no' => 9, 'colorclass' => 'yellow', 'step' => 15],

            ['status_name' => 'Details Verification Pending 3 Days Reject', 'slug' => 'details_verification_pending_3_days_reject', 'priority_no' => 10, 'colorclass' => 'red', 'step' => 16],

            ['status_name' => 'Appointment Booked Pending Warning', 'slug' => 'appointment_booked_pending_warning', 'priority_no' => 11, 'colorclass' => 'yellow', 'step' => 17],

            ['status_name' => 'Appointment Booked Pending Reject', 'slug' => 'appointment_booked_pending_reject', 'priority_no' => 12, 'colorclass' => 'red', 'step' => 18],

            ['status_name' => 'Appointment Scheduled', 'slug' => 'appointment_scheduled', 'priority_no' => 13, 'colorclass' => 'orange', 'step' => 19],

            ['status_name' => 'Appointment Rescheduled 1', 'slug' => 'appointment_rescheduled1', 'priority_no' => 14, 'colorclass' => 'orange', 'step' => 20],

            ['status_name' => 'Appointment Rescheduled 2', 'slug' => 'appointment_rescheduled2', 'priority_no' => 15, 'colorclass' => 'orange', 'step' => 21],

            ['status_name' => 'Appointment Rescheduled 3', 'slug' => 'appointment_rescheduled3', 'priority_no' => 16, 'colorclass' => 'orange', 'step' => 22],

            ['status_name' => 'POV Success', 'slug' => 'pov_success', 'priority_no' => 17, 'colorclass' => 'green', 'step' => 23],

            ['status_name' => 'POV Failed', 'slug' => 'pov_failed', 'priority_no' => 18, 'colorclass' => 'red', 'step' => 24],

            ['status_name' => 'POV Insufficient Documents', 'slug' => 'pov_insufficient_documents', 'priority_no' => 19, 'colorclass' => 'pink', 'step' => 25],

            ['status_name' => 'Not Intersted', 'slug' => 'not_intersted', 'priority_no' => 20, 'colorclass' => 'red', 'step' => 26],
        ];

        foreach ($statuses as $status) {
            ApplicationStatus::updateOrCreate(
                ['slug' => $status['slug']],
                $status
            );
        }
    }
}
