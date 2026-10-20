<?php

namespace App\Console\Commands;

use App\Models\ApplicationProgress;
use App\Models\Customer;
use App\Models\SmsLog;
use App\Services\InteraktService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class VerificationOhkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verification-ohk:interakt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled Verification OHK WhatsApp messages';

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
    public function handle(): int
    {
        $now = now();
        $currentTime = $now->format('H:i');

        $cronjobs = [
            // '0' => ['11:49'],
            '0' => ['20:00'],
            '1' => ['10:00'],
            '2' => ['15:00'],
        ];

        $scheduleDay = null;
        $scheduleTime = null;

        foreach ($cronjobs as $day => $times) {
            if (in_array($currentTime, $times, true)) {
                $scheduleDay = (int) $day;
                $scheduleTime = $currentTime;
                break;
            }
        }

        if ($scheduleDay === null) {
            $this->info("No Verification OHK schedule at {$currentTime}.");

            return self::SUCCESS;
        }

        $this->info(
            "Verification OHK cycle {$scheduleDay} selected at {$scheduleTime}."
        );

        $createdDate = $now->copy()
            ->subDays($scheduleDay)
            ->toDateString();

        $cronName = 'Verification OHK - ' . $scheduleDay;

        $responses = [];
        $recipients = [];

        $latestProgressIds = ApplicationProgress::query()
            ->selectRaw('MAX(id)')
            ->whereNotNull('customer_id')
            ->groupBy('customer_id');

        $progressRecords = ApplicationProgress::query()
            ->whereIn('id', $latestProgressIds)
            ->whereHas('status', function ($query) {
                $query->where('slug', 'verification_ohk');
            })
            ->whereNotNull('status_date')
            ->whereDate('status_date', $createdDate)
            ->get();

        $previousLogs = SmsLog::query()
            ->where('type', 'interakt')
            ->where('crontype', 'verification ohk')
            ->where('cronname', $cronName)
            ->get();

        $alreadySentProgress = [];
        $alreadySentMobiles = [];

        foreach ($previousLogs as $log) {
            $previousResponses = json_decode(
                $log->msgresponse ?? '[]',
                true
            );

            if (!is_array($previousResponses)) {
                continue;
            }

            foreach ($previousResponses as $item) {
                if (empty($item['status'])) {
                    continue;
                }

                if (isset($item['progress_id'])) {
                    $alreadySentProgress[$item['progress_id']] = true;
                }

                if (!empty($item['mobile'])) {
                    $alreadySentMobiles[trim($item['mobile'])] = true;
                }
            }
        }

        foreach ($progressRecords as $progress) {
            if (isset($alreadySentProgress[$progress->id])) {
                continue;
            }

            $customer = Customer::find($progress->customer_id);

            if (
                !$customer ||
                empty($customer->mobile_number) ||
                !$customer->is_active ||
                $customer->is_dnd
            ) {
                continue;
            }

            $mobile = trim($customer->mobile_number);

            if (
                isset($alreadySentMobiles[$mobile]) ||
                isset($recipients[$mobile])
            ) {
                continue;
            }

            $stillEligible = ApplicationProgress::query()
                ->whereKey($progress->id)
                ->whereHas('status', function ($query) {
                    $query->where('slug', 'verification_ohk');
                })
                ->exists();

            if (!$stillEligible) {
                continue;
            }

            $recipients[$mobile] = [
                'mobile' => $mobile,
                'name' => $customer->full_name ?: 'Customer',
                'customer_id' => $customer->id,
                'progress_id' => $progress->id,
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
                $number = preg_replace('/[\s\-()]/', '', $number);

                if (str_starts_with($number, '+91')) {
                    $number = substr($number, 3);
                } elseif (
                    str_starts_with($number, '91') &&
                    strlen($number) === 12
                ) {
                    $number = substr($number, 2);
                }

                if (!preg_match('/^[6-9]\d{9}$/', $number)) {
                    $this->warn("Invalid test number skipped: {$number}");
                    continue;
                }

                if (
                    isset($alreadySentMobiles[$number]) ||
                    isset($recipients[$number])
                ) {
                    continue;
                }

                $recipients[$number] = [
                    'mobile' => $number,
                    'name' => 'Test User',
                    'customer_id' => null,
                    'progress_id' => null,
                    'is_test' => true,
                ];
            }
        }

        if (empty($recipients)) {
            $this->info('No eligible Verification OHK recipients found.');

            $responses[] = [
                'status' => false,
                'message' => 'No eligible recipients found.',
                'cycle' => $scheduleDay,
            ];
        }

        $interaktService = app(InteraktService::class);

        foreach ($recipients as $data) {
            try {
                $response = $interaktService->sendVerificationOhk(
                    $data['mobile'],
                    $data['name'],
                );

                $success = $response['status'] ?? false;

                $responses[] = [
                    'status' => $success,
                    'mobile' => $data['mobile'],
                    // 'customer_id' => $data['customer_id'],
                    // 'progress_id' => $data['progress_id'],
                    // 'is_test' => $data['is_test'],
                    'response' => $response,
                ];

                if ($success) {
                    $this->info(
                        'WhatsApp request accepted for ' . $data['mobile']
                    );
                } else {
                    Log::error('VERIFICATION OHK SEND FAILED', [
                        'mobile' => $data['mobile'],
                        'customer_id' => $data['customer_id'],
                        'progress_id' => $data['progress_id'],
                        'response' => $response,
                    ]);
                }
            } catch (Throwable $e) {
                $responses[] = [
                    'status' => false,
                    'mobile' => $data['mobile'],
                    // 'customer_id' => $data['customer_id'],
                    // 'progress_id' => $data['progress_id'],
                    // 'is_test' => $data['is_test'],
                    'message' => $e->getMessage(),
                ];

                Log::error('VERIFICATION OHK COMMAND ERROR', [
                    'mobile' => $data['mobile'],
                    'error' => $e->getMessage(),
                ]);
            } finally {
                sleep(2);
            }
        }

        SmsLog::create([
            'type' => 'interakt',
            'crontype' => 'verification ohk',
            'cronname' => $cronName,
            'msgcount' => count($recipients),
            'msgresponse' => json_encode($responses),
        ]);

        $this->info(
            'Verification OHK messages processed: ' . count($recipients)
        );

        $this->info('Verification OHK SMS log created successfully.');

        return self::SUCCESS;
    }
}
