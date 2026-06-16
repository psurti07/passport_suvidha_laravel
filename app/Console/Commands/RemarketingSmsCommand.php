<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\SmsLog;
use App\Models\MessageTemplate;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;

class RemarketingSmsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remarketing:sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Passport Remarketing SMS';

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
    public function handle(SmsService $smsService)
    {
        $currentTime = now()->format('H:i');

        $cronjobs = [
            '0' => ['21:00'],
            '1' => ['10:30'],
            '2' => ['13:00'],
            '3' => ['15:00'],
            '5' => ['19:00'],
        ];

        $scheduleDay = null;

        foreach ($cronjobs as $day => $times) {
            if (in_array($currentTime, $times)) {
                $scheduleDay = (int)$day;
                break;
            }
        }

        if (is_null($scheduleDay)) {
            $this->info('No Schedule Found');

            return 0;
        }

        $createdDate = Carbon::now()->subDays($scheduleDay)->toDateString();

        $users = DB::table('customers')
            ->whereDate('created_at', $createdDate)
            ->where('is_paid', 0)
            ->where('is_active', 1)
            ->where('is_dnd', 0)
            ->whereNull('deleted_at')
            ->pluck('mobile_number');

        $mobiles = $users
            ->filter()
            ->map(function ($mobile) {

                return trim($mobile);
            })
            ->unique()
            ->values()
            ->toArray();

        if (config('services.sms.test_mode')) {
            $testNumbers = array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        config('services.testnumbers.test_numbers', '')
                    )
                )
            );

            $mobiles = array_unique(
                array_merge(
                    $mobiles,
                    $testNumbers
                )
            );
        }

        if (empty($mobiles)) {
            $response = [
                'status' => false,
                'message' => 'No Mobiles Found'

            ];
        } else {
            $message = MessageTemplate::where(
                'slug',
                'remarketing-sms'
            )
                ->value('message');

            if (!$message) {
                $response = [
                    'status' => false,
                    'message' => 'SMS Template Not Found'
                ];
            } else {
                $response = $smsService->sendBulkSms(
                    $mobiles,
                    $message
                );
            }
        }

        SmsLog::create([
            'type' => 'sms',
            'crontype' => 'customer sms',
            'cronname' => 'SMS - ' . $scheduleDay,
            'msgcount' => count($mobiles),
            'msgresponse' => json_encode($response, JSON_UNESCAPED_SLASHES),
        ]);

        $this->info(
            'SMS Sent : ' . count($mobiles)
        );

        return 0;
    }
}
