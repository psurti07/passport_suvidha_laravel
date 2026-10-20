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

        $cronjobs = [
            '1' => ['17:00'],
            '2' => ['10:00'],
            '3' => ['16:00'],
        ];

        $scheduleDay = null;
        $scheduleTime = null;

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

        $threeDaysAgo = $now->copy()->subDays(3);

        $cronName =
            'Details Verification Reminder - '
            . $scheduleDay;

        $documentService = app(SecureDocumentService::class);
        $interaktService = app(InteraktService::class);

        $responses = [];
        $mobiles = [];

        $latestProgressIds = ApplicationProgress::query()
            ->selectRaw('MAX(id)')
            ->whereNotNull('customer_id')
            ->groupBy('customer_id');

        $progressRecords = ApplicationProgress::query()
            ->whereIn('id', $latestProgressIds)
            ->whereHas('status', function ($query) {
                $query->where('slug', 'details_verification');
            })
            ->whereNotNull('status_date')
            ->whereBetween('status_date', [$threeDaysAgo, $now])
            ->get();

        $previousLogs = SmsLog::query()
            ->where('type', 'interakt')
            ->where('crontype', 'details verification reminder')
            ->where('cronname', $cronName)
            ->get();

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

        foreach ($progressRecords as $progress) {

            if (isset($alreadySent[$progress->id])) {
                continue;
            }

            $customer = Customer::find($progress->customer_id);

            if (!$customer) {
                continue;
            }

            if (empty($customer->mobile_number)) {
                continue;
            }

            if (!$customer->is_active) {
                continue;
            }

            if ($customer->is_dnd) {
                continue;
            }

            $mobile = trim($customer->mobile_number);

            if (isset($mobiles[$mobile])) {
                continue;
            }

            $finalDetail = FinalDetail::where(
                'customer_id',
                $customer->id
            )->latest('id')->first();

            if (!$finalDetail) {

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

                $responses[] = [
                    'status' => false,
                    'progress_id' => $progress->id,
                    'customer_id' => $customer->id,
                    'mobile' => $mobile,
                    'message' => 'FinalDetail document not found.',
                ];

                continue;
            }

            $stillEligible = ApplicationProgress::query()
                ->whereKey($progress->id)
                ->whereHas('status', function ($query) {
                    $query->where('slug', 'details_verification');
                })
                ->exists();

            if (!$stillEligible) {
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
            }
        }

        if (empty($mobiles)) {

            $responses[] = [
                'status' => false,
                'message' => 'No eligible recipients found. Check customer records and test configuration.',
            ];

            $this->warn('No eligible recipients found.');
        }

        foreach ($mobiles as $mobile => $data) {

            try {

                $response = $interaktService->sendDocument(
                    $mobile,
                    $data['name'],
                    $data['document_url'],
                    'details_verification'
                );

                $success = $response['status'] ?? false;

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

                Log::error(
                    'DETAILS VERIFICATION REMINDER: EXCEPTION',
                    [
                        'mobile' => $mobile,
                        'customer_id' => $data['customer_id'],
                        'progress_id' => $data['progress_id'],
                        'is_test' => $data['is_test'],
                        'error' => $e->getMessage(),
                        'exception' => get_class($e),
                    ]
                );
            }
        }

        SmsLog::create([
            'type' => 'interakt',
            'crontype' => 'details verification reminder',
            'cronname' => $cronName,
            'msgcount' => count($mobiles),
            'msgresponse' => json_encode($responses),
        ]);

        $this->info(
            'Details Verification Reminders Processed: '
                . count($mobiles)
        );

        return 0;
    }
}
