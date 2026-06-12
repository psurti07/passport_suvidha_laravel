<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MessageTemplate;

class MessageTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $messages = [

            ['slug' => 'otp-sms', 'name' => 'OTP SMS'],

            ['slug' => 'login-otp-sms', 'name' => 'Login OTP SMS'],

            ['slug' => 'complete-process-sms', 'name' => 'Complete Process SMS'],

            ['slug' => 'application-submitted-sms', 'name' => 'Application Submitted SMS'],

            ['slug' => 'account-sms', 'name' => 'Account SMS'],

            ['slug' => 'payment-failed-sms', 'name' => 'Payment Failed SMS'],

            ['slug' => 'welcome-sms', 'name' => 'Welcome SMS'],


            ['slug' => 'application-in-process-sms', 'name' => 'Application In Process SMS'],

            ['slug' => 'documents-submitted-sms', 'name' => 'Documents Submitted SMS'],

            ['slug' => 'details-verification-sms', 'name' => 'Details Verification SMS'],

            ['slug' => 'appointment-scheduled-sms', 'name' => 'Appointment Scheduled SMS'],

            ['slug' => 'appointment-rescheduled-sms', 'name' => 'Appointment Rescheduled SMS'],

            ['slug' => 'pov-success-sms', 'name' => 'POV Success SMS'],

            ['slug' => 'pov-failed-sms', 'name' => 'POV Failed SMS'],

            ['slug' => 'pov-insufficient-documents-sms', 'name' => 'POV Insufficient Documents SMS'],


            ['slug' => 'ticket-open-sms', 'name' => 'Ticket Open SMS'],

            ['slug' => 'ticket-in-progress-sms', 'name' => 'Ticket In Progress SMS'],

            ['slug' => 'ticket-closed-sms', 'name' => 'Ticket Closed SMS'],

        ];


        foreach ($messages as $item) {

            MessageTemplate::updateOrCreate(
                [
                    'slug' => $item['slug']
                ],
                [
                    'name' => $item['name']
                ]
            );

        }
    }
}
