<?php

namespace App\Console\Commands;

use App\Models\ApplicationProgress;
use App\Models\Customer;
use App\Models\FinalDetail;
use App\Models\SmsLog;
use App\Services\InteraktService;
use App\Services\SecureDocumentService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class DetailsVerificationReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'detailsVerificationReminder:interakt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send details verification WhatsApp document reminders';

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

        // Log::info('DETAILS VERIFICATION REMINDER: COMMAND STARTED', [
        //     'datetime' => $now->toDateTimeString(),
        //     'time' => $currentTime,
        //     'timezone' => config('app.timezone'),
        // ]);

        $cronjobs = [
            '1' => ['17:00'],
            '2' => ['10:00'],
            '3' => ['16:00'],
        ];

        // Log::info('DETAILS VERIFICATION REMINDER: CHECKING SCHEDULE', [
        //     'current_time' => $currentTime,
        //     'cronjobs' => $cronjobs,
        // ]);

        $scheduleDay = null;
        $scheduleTime = null;

        /*
     * Allow the command to run within 15 minutes of the scheduled time.
     *
     * Example:
     * 16:00 - 16:14 => 16:00 cycle
     */
        $current = \Carbon\Carbon::createFromFormat('H:i', $currentTime);

        foreach ($cronjobs as $day => $times) {
            foreach ($times as $time) {

                $scheduled = \Carbon\Carbon::createFromFormat('H:i', $time);

                if (
                    $current->greaterThanOrEqualTo($scheduled) &&
                    $current->lessThan(
                        $scheduled->copy()->addMinutes(15)
                    )
                ) {
                    $scheduleDay = (int) $day;
                    $scheduleTime = $time;

                    break 2;
                }
            }
        }

        if (is_null($scheduleDay)) {

            // Log::warning(
            //     'DETAILS VERIFICATION REMINDER: NO SCHEDULE FOUND',
            //     [
            //         'current_time' => $currentTime,
            //         'datetime' => $now->toDateTimeString(),
            //         'timezone' => config('app.timezone'),
            //     ]
            // );

            $this->info('No Schedule Found');

            return 0;
        }

        // Log::info(
        //     'DETAILS VERIFICATION REMINDER: SCHEDULE FOUND',
        //     [
        //         'schedule_day' => $scheduleDay,
        //         'schedule_time' => $scheduleTime,
        //         'current_time' => $currentTime,
        //         'datetime' => $now->toDateTimeString(),
        //     ]
        // );

        $threeDaysAgo = $now->copy()->subDays(3);

        $cronName =
            'Details Verification Reminder - '
            . $scheduleDay;

        // Log::info(
        //     'DETAILS VERIFICATION REMINDER: INITIALIZING SERVICES',
        //     [
        //         'cron_name' => $cronName,
        //         'from_date' => $threeDaysAgo->toDateTimeString(),
        //         'to_date' => $now->toDateTimeString(),
        //     ]
        // );

        $documentService = app(SecureDocumentService::class);
        $interaktService = app(InteraktService::class);

        $responses = [];
        $mobiles = [];

        /*
     * Get latest progress ID for every customer.
     */
        $latestProgressIds = ApplicationProgress::query()
            ->selectRaw('MAX(id)')
            ->whereNotNull('customer_id')
            ->groupBy('customer_id');

        // Log::info(
        //     'DETAILS VERIFICATION REMINDER: FETCHING PROGRESS RECORDS'
        // );

        $progressRecords = ApplicationProgress::query()
            ->whereIn('id', $latestProgressIds)
            ->whereHas('status', function ($query) {
                $query->where('slug', 'details_verification');
            })
            ->whereNotNull('status_date')
            ->whereBetween('status_date', [$threeDaysAgo, $now])
            ->get();

        // Log::info(
        //     'DETAILS VERIFICATION REMINDER: PROGRESS RECORDS FOUND',
        //     [
        //         'count' => $progressRecords->count(),
        //     ]
        // );

        /*
     * Find successful sends for this specific schedule slot.
     */
        $previousLogs = SmsLog::query()
            ->where('type', 'interakt')
            ->where('crontype', 'details verification reminder')
            ->where('cronname', $cronName)
            ->get();

        // Log::info(
        //     'DETAILS VERIFICATION REMINDER: PREVIOUS LOGS FOUND',
        //     [
        //         'count' => $previousLogs->count(),
        //         'cron_name' => $cronName,
        //     ]
        // );

        $alreadySent = [];

        foreach ($previousLogs as $log) {

            $previousResponses = json_decode(
                $log->msgresponse ?? '[]',
                true
            );

            if (!is_array($previousResponses)) {
                continue;
            }

            foreach ($previousResponses as $item) {

                if (
                    !empty($item['status']) &&
                    isset($item['progress_id'])
                ) {
                    $alreadySent[$item['progress_id']] = true;
                }
            }
        }

        // Log::info(
        //     'DETAILS VERIFICATION REMINDER: ALREADY SENT PROGRESS',
        //     [
        //         'count' => count($alreadySent),
        //         'progress_ids' => array_keys($alreadySent),
        //     ]
        // );

        /*
     * Add eligible customers.
     */
        foreach ($progressRecords as $progress) {

            // Log::info(
            //     'DETAILS VERIFICATION REMINDER: PROCESSING PROGRESS',
            //     [
            //         'progress_id' => $progress->id,
            //         'customer_id' => $progress->customer_id,
            //         'status_date' => $progress->status_date,
            //     ]
            // );

            /*
         * Already sent in this schedule cycle.
         */
            if (isset($alreadySent[$progress->id])) {

                // Log::info(
                //     'DETAILS VERIFICATION REMINDER: SKIPPED ALREADY SENT',
                //     [
                //         'progress_id' => $progress->id,
                //         'customer_id' => $progress->customer_id,
                //         'cron_name' => $cronName,
                //     ]
                // );

                continue;
            }

            $customer = Customer::find($progress->customer_id);

            if (!$customer) {

                // Log::warning(
                //     'DETAILS VERIFICATION REMINDER: CUSTOMER NOT FOUND',
                //     [
                //         'progress_id' => $progress->id,
                //         'customer_id' => $progress->customer_id,
                //     ]
                // );

                continue;
            }

            // Log::info(
            //     'DETAILS VERIFICATION REMINDER: CUSTOMER FOUND',
            //     [
            //         'progress_id' => $progress->id,
            //         'customer_id' => $customer->id,
            //         'is_active' => $customer->is_active,
            //         'is_dnd' => $customer->is_dnd,
            //         'has_mobile' => !empty($customer->mobile_number),
            //     ]
            // );

            /*
         * Check customer eligibility.
         */
            if (empty($customer->mobile_number)) {

                // Log::warning(
                //     'DETAILS VERIFICATION REMINDER: SKIPPED NO MOBILE',
                //     [
                //         'progress_id' => $progress->id,
                //         'customer_id' => $customer->id,
                //     ]
                // );

                continue;
            }

            if (!$customer->is_active) {

                // Log::warning(
                //     'DETAILS VERIFICATION REMINDER: SKIPPED INACTIVE CUSTOMER',
                //     [
                //         'progress_id' => $progress->id,
                //         'customer_id' => $customer->id,
                //     ]
                // );

                continue;
            }

            if ($customer->is_dnd) {

                // Log::warning(
                //     'DETAILS VERIFICATION REMINDER: SKIPPED DND CUSTOMER',
                //     [
                //         'progress_id' => $progress->id,
                //         'customer_id' => $customer->id,
                //     ]
                // );

                continue;
            }

            $mobile = trim($customer->mobile_number);

            /*
         * Avoid duplicate mobile numbers in the same execution.
         */
            if (isset($mobiles[$mobile])) {

                // Log::warning(
                //     'DETAILS VERIFICATION REMINDER: DUPLICATE MOBILE SKIPPED',
                //     [
                //         'progress_id' => $progress->id,
                //         'customer_id' => $customer->id,
                //         'mobile' => $mobile,
                //     ]
                // );

                continue;
            }

            $finalDetail = FinalDetail::where(
                'customer_id',
                $customer->id
            )->latest('id')->first();

            if (!$finalDetail) {

                // Log::warning(
                //     'DETAILS VERIFICATION REMINDER: FINAL DETAIL NOT FOUND',
                //     [
                //         'progress_id' => $progress->id,
                //         'customer_id' => $customer->id,
                //         'mobile' => $mobile,
                //     ]
                // );

                $responses[] = [
                    'status' => false,
                    'progress_id' => $progress->id,
                    'customer_id' => $customer->id,
                    'mobile' => $mobile,
                    'message' => 'FinalDetail record not found.',
                ];

                continue;
            }

            if (!$documentService->fileExists($finalDetail)) {

                // Log::warning(
                //     'DETAILS VERIFICATION REMINDER: DOCUMENT FILE NOT FOUND',
                //     [
                //         'progress_id' => $progress->id,
                //         'customer_id' => $customer->id,
                //         'final_detail_id' => $finalDetail->id,
                //         'mobile' => $mobile,
                //     ]
                // );

                $responses[] = [
                    'status' => false,
                    'progress_id' => $progress->id,
                    'customer_id' => $customer->id,
                    'mobile' => $mobile,
                    'message' => 'FinalDetail document not found.',
                ];

                continue;
            }

            /*
         * Recheck eligibility before sending.
         */
            $stillEligible = ApplicationProgress::query()
                ->whereKey($progress->id)
                ->whereHas('status', function ($query) {
                    $query->where('slug', 'details_verification');
                })
                ->exists();

            if (!$stillEligible) {

                // Log::warning(
                //     'DETAILS VERIFICATION REMINDER: STATUS CHANGED',
                //     [
                //         'progress_id' => $progress->id,
                //         'customer_id' => $customer->id,
                //     ]
                // );

                continue;
            }

            $documentUrl = $documentService->generateUrl(
                'final_details',
                $finalDetail->id
            );

            $mobiles[$mobile] = [
                'customer_id' => $customer->id,
                'progress_id' => $progress->id,
                'name' => $customer->full_name ?: 'Customer',
                'document_id' => $finalDetail->id,
                'document_url' => $documentUrl,
                'is_test' => false,
            ];

            // Log::info(
            //     'DETAILS VERIFICATION REMINDER: RECIPIENT ADDED',
            //     [
            //         'progress_id' => $progress->id,
            //         'customer_id' => $customer->id,
            //         'mobile' => $mobile,
            //         'document_id' => $finalDetail->id,
            //     ]
            // );
        }

        /*
     * Add test numbers.
     */
        if (config('services.interakt.test_mode')) {

            // Log::info(
            //     'DETAILS VERIFICATION REMINDER: TEST MODE ENABLED'
            // );

            $testNumbers = array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        config('services.testnumbers.test_numbers', '')
                    )
                )
            );

            // Log::info(
            //     'DETAILS VERIFICATION REMINDER: TEST NUMBERS',
            //     [
            //         'count' => count($testNumbers),
            //     ]
            // );

            foreach ($testNumbers as $number) {

                if (isset($mobiles[$number])) {

                    // Log::info(
                    //     'DETAILS VERIFICATION REMINDER: TEST NUMBER ALREADY ADDED',
                    //     [
                    //         'mobile' => $number,
                    //     ]
                    // );

                    continue;
                }

                $documentUrl = 'https://passportsuvidha.com/';
                $documentId = null;

                $mobiles[$number] = [
                    'customer_id' => null,
                    'progress_id' => null,
                    'name' => 'Test User',
                    'document_id' => $documentId,
                    'document_url' => $documentUrl,
                    'is_test' => true,
                ];

                // Log::info(
                //     'DETAILS VERIFICATION REMINDER: TEST RECIPIENT ADDED',
                //     [
                //         'mobile' => $number,
                //     ]
                // );
            }
        }

        // Log::info(
        //     'DETAILS VERIFICATION REMINDER: RECIPIENTS READY',
        //     [
        //         'count' => count($mobiles),
        //     ]
        // );

        if (empty($mobiles)) {

            // Log::warning(
            //     'DETAILS VERIFICATION REMINDER: NO ELIGIBLE RECIPIENTS'
            // );

            $responses[] = [
                'status' => false,
                'message' => 'No eligible recipients found. Check customer records and test configuration.',
            ];

            $this->warn('No eligible recipients found.');
        }

        /*
     * Send messages.
     */
        foreach ($mobiles as $mobile => $data) {

            // Log::info(
            //     'DETAILS VERIFICATION REMINDER: SENDING INTERAKT MESSAGE',
            //     [
            //         'mobile' => $mobile,
            //         'customer_id' => $data['customer_id'],
            //         'progress_id' => $data['progress_id'],
            //         'is_test' => $data['is_test'],
            //     ]
            // );

            try {

                $response = $interaktService->sendDocument(
                    $mobile,
                    $data['name'],
                    $data['document_url'],
                    'details_verification'
                );

                $success = $response['status'] ?? false;

                // Log::info(
                //     'DETAILS VERIFICATION REMINDER: INTERAKT RESPONSE',
                //     [
                //         'mobile' => $mobile,
                //         'customer_id' => $data['customer_id'],
                //         'progress_id' => $data['progress_id'],
                //         'success' => $success,
                //         'response' => $response,
                //     ]
                // );

                $responses[] = [
                    'status' => $success,
                    'mobile' => $mobile,
                    // 'customer_id' => $data['customer_id'],
                    // 'progress_id' => $data['progress_id'],
                    // 'document_id' => $data['document_id'],
                    // 'is_test' => $data['is_test'],
                    // 'reminder_date' => $now->toDateString(),
                    // 'reminder_time' => $currentTime,
                    'response' => $response,
                ];

                if (!$success) {

                    Log::error(
                        'DETAILS VERIFICATION REMINDER: INTERAKT SEND FAILED',
                        [
                            'mobile' => $mobile,
                            'customer_id' => $data['customer_id'],
                            'progress_id' => $data['progress_id'],
                            'is_test' => $data['is_test'],
                            'response' => $response,
                        ]
                    );
                }
                // } else {

                // Log::info(
                //     'DETAILS VERIFICATION REMINDER: INTERAKT SEND SUCCESS',
                //     [
                //         'mobile' => $mobile,
                //         'customer_id' => $data['customer_id'],
                //         'progress_id' => $data['progress_id'],
                //         'is_test' => $data['is_test'],
                //     ]
                // );
                // }

                sleep(2);
            } catch (Throwable $e) {

                $responses[] = [
                    'status' => false,
                    'mobile' => $mobile,
                    'customer_id' => $data['customer_id'],
                    'progress_id' => $data['progress_id'],
                    'is_test' => $data['is_test'],
                    'message' => $e->getMessage(),
                ];

                // Log::error(
                //     'DETAILS VERIFICATION REMINDER: EXCEPTION',
                //     [
                //         'mobile' => $mobile,
                //         'customer_id' => $data['customer_id'],
                //         'progress_id' => $data['progress_id'],
                //         'is_test' => $data['is_test'],
                //         'error' => $e->getMessage(),
                //         'exception' => get_class($e),
                //     ]
                // );
            }
        }

        /*
     * Save execution log.
     */
        // Log::info(
        //     'DETAILS VERIFICATION REMINDER: CREATING SMS LOG',
        //     [
        //         'cron_name' => $cronName,
        //         'schedule_day' => $scheduleDay,
        //         'schedule_time' => $scheduleTime,
        //         'recipient_count' => count($mobiles),
        //         'response_count' => count($responses),
        //     ]
        // );

        SmsLog::create([
            'type' => 'interakt',
            'crontype' => 'details verification reminder',
            'cronname' => $cronName,
            'msgcount' => count($mobiles),
            'msgresponse' => json_encode($responses),
        ]);

        // Log::info(
        //     'DETAILS VERIFICATION REMINDER: COMMAND COMPLETED',
        //     [
        //         'cron_name' => $cronName,
        //         'schedule_day' => $scheduleDay,
        //         'schedule_time' => $scheduleTime,
        //         'recipient_count' => count($mobiles),
        //         'response_count' => count($responses),
        //     ]
        // );

        $this->info(
            'Details Verification Reminders Processed: '
                . count($mobiles)
        );

        return 0;
    }
}
