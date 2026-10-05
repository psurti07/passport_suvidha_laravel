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
        $currentTime = now()->format('H:i');
        $cronjobs = ['0' => ['10:00', '21:00'],];
        $scheduleDay = null;

        foreach ($cronjobs as $day => $times) {

            if (in_array($currentTime, $times)) {

                $scheduleDay = (int) $day;
                break;
            }
        }

        if (is_null($scheduleDay)) {

            $this->info('No Schedule Found');

            return 0;
        }

        $tomorrow = Carbon::tomorrow()->toDateString();
        $documentService = app(SecureDocumentService::class);
        $interaktService = app(InteraktService::class);

        $responses = [];
        $mobiles = [];

        $appointmentLetters = AppointmentLetter::query()
            ->whereDate('appointment_date', $tomorrow)
            ->whereNotNull('customer_id')
            ->get();

        foreach ($appointmentLetters as $appointment) {

            $customer = Customer::find($appointment->customer_id);
            if (!$customer) {
                continue;
            }

            if (
                empty($customer->mobile_number) ||
                !$customer->is_active ||
                $customer->is_dnd
            ) {
                continue;
            }

            if (
                empty($appointment->file_path) ||
                !$documentService->fileExists($appointment)
            ) {
                $responses[] = [
                    'status' => false,
                    'customer_id' => $customer->id,
                    'message' => 'Appointment document not found.',
                ];
                continue;
            }

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
        }

        if (config('services.interakt.test_mode')) {
            $testNumbers = array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        config('services.testnumbers.test_numbers', '')
                    )
                )
            );

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

        if (empty($mobiles)) {
            $responses[] = [
                'status' => false,
                'message' => 'No eligible recipients found. Check appointments and test configuration.',
            ];

            $this->warn('No eligible recipients found.');
        }

        foreach ($mobiles as $mobile => $data) {
            try {
                $response = $interaktService->sendDocument(
                    $mobile,
                    $data['name'],
                    $data['document_url'],
                    'appointment'
                );

                $responses[] = [
                    'status' => $response['status'] ?? false,
                    'mobile' => $mobile,
                    'customer_id' => $data['customer_id'],
                    'appointment_date' => $data['appointment_date'],
                    'appointment_time' => $data['appointment_time'],
                    'response' => $response,
                ];
            } catch (Throwable $e) {
                $responses[] = [
                    'status' => false,
                    'mobile' => $mobile,
                    'customer_id' => $data['customer_id'],
                    'message' => $e->getMessage(),
                ];

                Log::error('Appointment reminder failed', [
                    'mobile' => $mobile,
                    'customer_id' => $data['customer_id'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        SmsLog::create([
            'type' => 'interakt',
            'crontype' => 'appointment reminder',
            'cronname' => 'Appointment Reminder - ' . $scheduleDay,
            'msgcount' => count($mobiles),
            'msgresponse' => json_encode($responses),
        ]);

        $this->info(
            'Appointment Reminder Messages Processed: ' . count($mobiles)
        );

        return 0;
    }
}
