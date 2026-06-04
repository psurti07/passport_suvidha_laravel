<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\RcsService;
use App\Models\SmsLog;

class RemarketingRcsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remarketing:rcs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send RCS Remarketing Messages';

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
            '1' => ['08:00'],
            '2' => ['10:30'],
            '3' => ['13:00'],
            '4' => ['15:15'],
            '5' => ['17:30'],
            '6' => ['19:00'],
            '7' => ['21:45'],
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

        $users = DB::table('customers as c')
            ->select(
                'c.id',
                'c.first_name',
                'c.last_name',
                'c.mobile_number',
                'c.email',
                'c.created_at'
            )
            ->whereDate(
                'c.created_at',
                Carbon::now()->subDays($scheduleDay)->toDateString()
            )
            ->where('c.is_paid', 0)
            ->where('c.is_active', 1)
            ->where('c.is_dnd', 0)
            ->whereNull('c.deleted_at')
            ->distinct()
            ->orderBy('c.id', 'asc')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No Customers Found');
            return 0;
        }

        $mobiles = $users
            ->pluck('mobile_number')
            ->filter()
            ->map(function ($mobile) {
                return '91' . trim($mobile);
            })
            ->unique()
            ->values()
            ->toArray();

        $testNumbers = array_filter(
            array_map(
                'trim',
                explode(',', config('services.rcs.test_numbers', ''))
            )
        );

        $mobiles = array_unique(
            array_merge($mobiles, $testNumbers)
        );

        $response = app(RcsService::class)
            ->send($mobiles);

        SmsLog::create([
            'type' => 'rcs',
            'crontype' => 'customer rcs',
            'cronname' => 'RCS - ' . $scheduleDay,
            'msgcount' => count($mobiles),
            'msgresponse' => json_encode($response),
        ]);

        $this->info('RCS Sent Successfully');

        return 0;
    }
}
