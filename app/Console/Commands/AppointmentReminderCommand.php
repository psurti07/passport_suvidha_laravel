<?php

namespace App\Console\Commands;

use App\Models\AppointmentLetter;
use App\Models\Customer;
use App\Models\SmsLog;
use App\Services\InteraktService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Services\SecureDocumentService;
use Illuminate\Support\Facades\Log;
use Throwable;

class AppointmentReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointmentReminder:interakt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send appointment reminder one day before appointment';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */


    public function handle()
    {
        $now = now();
        $currentTime = $now->format('H:i');

        // Log::info('APPOINTMENT REMINDER: COMMAND STARTED', [
        //     'datetime' => $now->toDateTimeString(),
        //     'time' => $currentTime,
        //     'timezone' => config('app.timezone'),
        // ]);

        $cronjobs = [
            '0' => ['10:00', '21:00'],
        ];

        // Log::info('APPOINTMENT REMINDER: CHECKING SCHEDULE', [
        //     'current_time' => $currentTime,
        //     'cronjobs' => $cronjobs,
        // ]);

        $scheduleDay = null;
        $scheduleTime = null;

        foreach ($cronjobs as $day => $times) {
            foreach ($times as $time) {
                $scheduled = Carbon::today(config('app.timezone'))
                    ->setTimeFromTimeString($time);

                $windowEnd = $scheduled->copy()->addMinutes(15);

                if (
                    $now->greaterThanOrEqualTo($scheduled) &&
                    $now->lessThan($windowEnd)
                ) {
                    $scheduleDay = (int) $day;
                    $scheduleTime = $time;
                    break 2;
                }
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Schedule Not Found
    |--------------------------------------------------------------------------
    */

        if (is_null($scheduleDay)) {

            // Log::warning('APPOINTMENT REMINDER: NO SCHEDULE FOUND', [
            //     'current_time' => $currentTime,
            //     'datetime' => $now->toDateTimeString(),
            //     'timezone' => config('app.timezone'),
            // ]);

            $this->info('No Schedule Found');

            return 0;
        }

        // Log::info('APPOINTMENT REMINDER: SCHEDULE FOUND', [
        //     'schedule_day' => $scheduleDay,
        //     'scheduled_time' => $scheduleTime,
        //     'current_time' => $currentTime,
        // ]);

        /*
    |--------------------------------------------------------------------------
    | Services
    |--------------------------------------------------------------------------
    */

        $tomorrow = Carbon::tomorrow()->toDateString();

        // Log::info('APPOINTMENT REMINDER: CHECKING APPOINTMENTS', [
        //     'appointment_date' => $tomorrow,
        // ]);

        $documentService = app(SecureDocumentService::class);
        $interaktService = app(InteraktService::class);

        $responses = [];
        $mobiles = [];

        /*
    |--------------------------------------------------------------------------
    | Get Appointment Letters
    |--------------------------------------------------------------------------
    */

        $appointmentLetters = AppointmentLetter::query()
            ->whereDate('appointment_date', $tomorrow)
            ->whereNotNull('customer_id')
            ->get();

        // Log::info('APPOINTMENT REMINDER: APPOINTMENTS FOUND', [
        //     'count' => $appointmentLetters->count(),
        //     'appointment_date' => $tomorrow,
        // ]);

        /*
    |--------------------------------------------------------------------------
    | Process Appointments
    |--------------------------------------------------------------------------
    */

        foreach ($appointmentLetters as $appointment) {

            // Log::info('APPOINTMENT REMINDER: PROCESSING APPOINTMENT', [
            //     'appointment_id' => $appointment->id,
            //     'customer_id' => $appointment->customer_id,
            //     'appointment_date' => $appointment->appointment_date,
            //     'appointment_time' => $appointment->appointment_time,
            // ]);

            $customer = Customer::find($appointment->customer_id);

            if (!$customer) {

                // Log::warning('APPOINTMENT REMINDER: CUSTOMER NOT FOUND', [
                //     'appointment_id' => $appointment->id,
                //     'customer_id' => $appointment->customer_id,
                // ]);

                continue;
            }

            // Log::info('APPOINTMENT REMINDER: CUSTOMER FOUND', [
            //     'customer_id' => $customer->id,
            //     'mobile' => $customer->mobile_number,
            //     'is_active' => $customer->is_active,
            //     'is_dnd' => $customer->is_dnd,
            // ]);

            /*
        |--------------------------------------------------------------------------
        | Customer Eligibility
        |--------------------------------------------------------------------------
        */

            if (
                empty($customer->mobile_number) ||
                !$customer->is_active ||
                $customer->is_dnd
            ) {

                // Log::warning('APPOINTMENT REMINDER: CUSTOMER NOT ELIGIBLE', [
                //     'customer_id' => $customer->id,
                //     'mobile' => $customer->mobile_number,
                //     'is_active' => $customer->is_active,
                //     'is_dnd' => $customer->is_dnd,
                // ]);

                continue;
            }

            /*
        |--------------------------------------------------------------------------
        | Check Appointment Document
        |--------------------------------------------------------------------------
        */

            if (
                empty($appointment->file_path) ||
                !$documentService->fileExists($appointment)
            ) {

                // Log::warning('APPOINTMENT REMINDER: DOCUMENT NOT FOUND', [
                //     'appointment_id' => $appointment->id,
                //     'customer_id' => $customer->id,
                //     'file_path' => $appointment->file_path,
                // ]);

                $responses[] = [
                    'status' => false,
                    'customer_id' => $customer->id,
                    'message' => 'Appointment document not found.',
                ];

                continue;
            }

            /*
        |--------------------------------------------------------------------------
        | Add Recipient
        |--------------------------------------------------------------------------
        */

            $mobile = trim($customer->mobile_number);

            $mobiles[$mobile] = [
                'customer_id' => $customer->id,
                'name' => $customer->full_name ?: 'Customer',
                'appointment_date' => $appointment->appointment_date,
                'appointment_time' => $appointment->appointment_time,
                'document_url' => $documentService->generateUrl(
                    'appointment_letters',
                    $appointment->id
                ),
            ];

            // Log::info('APPOINTMENT REMINDER: RECIPIENT ADDED', [
            //     'customer_id' => $customer->id,
            //     'mobile' => $mobile,
            // ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Test Mode
    |--------------------------------------------------------------------------
    */

        if (config('services.interakt.test_mode')) {

            // Log::info('APPOINTMENT REMINDER: TEST MODE ENABLED');

            $testNumbers = array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        config('services.testnumbers.test_numbers', '')
                    )
                )
            );

            // Log::info('APPOINTMENT REMINDER: TEST NUMBERS', [
            //     'count' => count($testNumbers),
            // ]);

            foreach ($testNumbers as $number) {

                if (isset($mobiles[$number])) {
                    continue;
                }

                $mobiles[$number] = [
                    'customer_id' => null,
                    'name' => 'Test User',
                    'appointment_date' => $tomorrow,
                    'appointment_time' => null,
                    'document_url' => 'signin',
                ];
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Final Recipients
    |--------------------------------------------------------------------------
    */

        // Log::info('APPOINTMENT REMINDER: RECIPIENTS READY', [
        //     'count' => count($mobiles),
        //     'mobiles' => array_keys($mobiles),
        // ]);

        if (empty($mobiles)) {

            Log::warning('APPOINTMENT REMINDER: NO ELIGIBLE RECIPIENTS', [
                'appointment_date' => $tomorrow,
            ]);

            $responses[] = [
                'status' => false,
                'message' => 'No eligible recipients found. Check appointments and test configuration.',
            ];

            $this->warn('No eligible recipients found.');
        }

        /*
    |--------------------------------------------------------------------------
    | Send Interakt Messages
    |--------------------------------------------------------------------------
    */

        foreach ($mobiles as $mobile => $data) {

            // Log::info('APPOINTMENT REMINDER: SENDING INTERAKT MESSAGE', [
            //     'mobile' => $mobile,
            //     'customer_id' => $data['customer_id'],
            // ]);

            try {

                $response = $interaktService->sendDocument(
                    $mobile,
                    $data['name'],
                    $data['document_url'],
                    'appointment'
                );

                // Log::info('APPOINTMENT REMINDER: INTERAKT RESPONSE', [
                //     'mobile' => $mobile,
                //     'customer_id' => $data['customer_id'],
                //     'response' => $response,
                // ]);

                sleep(2);

                $responses[] = [
                    'status' => $response['status'] ?? false,
                    'mobile' => $mobile,
                    // 'customer_id' => $data['customer_id'],
                    // 'appointment_date' => $data['appointment_date'],
                    // 'appointment_time' => $data['appointment_time'],
                    'response' => $response,
                ];

                // Log::info('APPOINTMENT REMINDER: MESSAGE PROCESSED', [
                //     'mobile' => $mobile,
                //     'status' => $response['status'] ?? false,
                // ]);
            } catch (Throwable $e) {

                Log::error('APPOINTMENT REMINDER: SEND ERROR', [
                    'mobile' => $mobile,
                    'customer_id' => $data['customer_id'],
                    'error' => $e->getMessage(),
                ]);

                $responses[] = [
                    'status' => false,
                    'mobile' => $mobile,
                    'customer_id' => $data['customer_id'],
                    'message' => $e->getMessage(),
                ];
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Save SMS Log
    |--------------------------------------------------------------------------
    */

        SmsLog::create([
            'type' => 'interakt',
            'crontype' => 'appointment reminder',
            'cronname' => 'Appointment Reminder - ' . $scheduleDay,
            'msgcount' => count($mobiles),
            'msgresponse' => json_encode($responses),
        ]);

        // Log::info('APPOINTMENT REMINDER: SMS LOG CREATED', [
        //     'schedule_day' => $scheduleDay,
        //     'message_count' => count($mobiles),
        // ]);

        /*
    |--------------------------------------------------------------------------
    | Complete
    |--------------------------------------------------------------------------
    */

        // Log::info('APPOINTMENT REMINDER: COMMAND COMPLETED', [
        //     'schedule_day' => $scheduleDay,
        //     'message_count' => count($mobiles),
        //     'datetime' => now()->toDateTimeString(),
        // ]);

        $this->info(
            'Appointment Reminder Messages Processed: ' . count($mobiles)
        );

        return 0;
    }
}
