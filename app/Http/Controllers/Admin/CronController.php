<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\SmsLog;
use App\Services\RcsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CronController extends Controller
{
    public function remarketingRcs($token)
    {
        // Security Check
        if ($token !== config('services.rcs.cron_token')) {
            abort(403);
        }

        // Current Time
        $currentTime = now()->format('H:i');

        // Cron Schedule (day => times)
        $cronjobs = [
            '0'  => ['12:45'],
        ];

        // Find schedule day
        $scheduleDay = null;

        foreach ($cronjobs as $day => $times) {
            if (in_array($currentTime, $times)) {
                $scheduleDay = (int) $day;
                break;
            }
        }

        if (is_null($scheduleDay)) {
            return response()->json([
                'status' => false,
                'message' => 'No schedule found',
                'time' => $currentTime,
            ]);
        }

        // Fetch customers 
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

        // No customers
        if ($users->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No customers found',
                'schedule_day' => $scheduleDay,
            ]);
        }

        // Build mobile list 
        $mobiles = $users
            ->pluck('mobile_number')
            ->filter()
            ->map(fn($m) => '91' . trim($m))
            ->unique()
            ->values()
            ->toArray();

        // Add test numbers 
        $testNumbers = [
            '919408881214',
        ];
        $mobiles = array_merge($mobiles, $testNumbers);
        $mobiles = array_unique($mobiles);

        // Send RCS
        $rcsService = new RcsService();
        $response = $rcsService->send($mobiles);

        // Log
        SmsLog::create([
            'crontype' => 'customer rcs',
            'parentid' => 31,
            'cronname' => 'RCS - ' . $scheduleDay,
            'msgcount' => $users->count(),
            'msgresponse' => json_encode($response),
        ]);

        // Response
        return response()->json([
            'status' => true,
            'time' => $currentTime,
            'schedule_day' => $scheduleDay,
            'total_customers' => $users->count(),
        ]);
    }
}
