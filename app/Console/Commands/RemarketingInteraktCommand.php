<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\SmsLog;
use App\Services\InteraktService;

class RemarketingInteraktCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remarketing:interakt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Interakt Remarketing Messages';

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

        $cronjobs = [
            '0' => ['23:30', '09:30'],
            '1' => ['09:00'],
            '2' => ['22:30', '11:30'],
            '3' => ['21:30', '12:30'],
            '4' => ['20:30'],
            '6' => ['13:30'],
            '8' => ['19:30'],
            '9' => ['14:30'],
            '10' => ['18:00'],
            '12' => ['15:30'],
            '13' => ['17:00'],
            '15' => ['16:30'],
            '16' => ['16:00'],
            '17' => ['10:30'],
            '20' => ['15:00'],
        ];

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

        $users = Customer::query()
            ->whereDate(
                'created_at',
                Carbon::now()->subDays($scheduleDay)->toDateString()
            )
            ->where('is_paid', 0)
            ->where('is_active', 1)
            ->where('is_dnd', 0)
            ->whereNull('deleted_at')
            ->orderBy('id', 'asc')
            ->get();

        $responses = [];

        $mobiles = [];

        foreach ($users as $user) {

            if (empty($user->mobile_number)) {
                continue;
            }

            $mobile = '+91' . trim($user->mobile_number);

            $mobiles[$mobile] = [
                'customer_id' => $user->id,
                'name' => $user->full_name,
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

                $mobiles[$number] = [
                    'customer_id' => null,
                    'name' => 'Test User',
                ];
            }
        }

        if (empty($mobiles)) {

            $responses[] = [
                'status' => false,
                'message' => 'No Mobiles Found',
            ];
        } else {

            foreach ($mobiles as $mobile => $data) {

                $response = app(InteraktService::class)
                    ->send(
                        $mobile,
                        $data['name']
                    );

                $responses[] = [
                    'customer_id' => $data['customer_id'],
                    'mobile' => $mobile,
                    'response' => $response,
                ];
            }
        }

        SmsLog::create([
            'type' => 'interakt',
            'crontype' => 'customer interakt',
            'cronname' => 'Interakt - ' . $scheduleDay,
            'msgcount' => count($responses),
            'msgresponse' => json_encode($responses),
        ]);

        $this->info(
            'Interakt Messages Sent : ' . count($responses)
        );

        return 0;
    }
}
