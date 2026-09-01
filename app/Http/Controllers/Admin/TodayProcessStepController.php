<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use App\Models\Customer;

class TodayProcessStepController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $step2OtpVerified = Customer::whereDate('created_at', $today)
            ->where('registration_step', 2)
            ->count();

        $step3FamilyDetails = Customer::whereDate('created_at', $today)
            ->where('registration_step', 3)
            ->count();

        $step4PersonalDetails = Customer::whereDate('created_at', $today)
            ->where('registration_step', 4)
            ->count();

        $step5IsCustomer = Customer::whereDate('created_at', $today)
            ->where('registration_step', 5)
            ->count();


        $processStats = [
            [
                'step' => 2,
                'count' => $step2OtpVerified,
                'label' => 'OTP Verified',
                'icon' => 'fa-key',
            ],
            [
                'step' => 3,
                'count' => $step3FamilyDetails,
                'label' => 'Family Details',
                'icon' => 'fa-users',
            ],
            [
                'step' => 4,
                'count' => $step4PersonalDetails,
                'label' => 'Personal Details',
                'icon' => 'fa-user',
            ],
            [
                'step' => 5,
                'count' => $step5IsCustomer,
                'label' => 'Payment Completed',
                'icon' => 'fa-check-circle',
            ],
        ];

        $currentDate = Date::now()->format('j M, Y');

        return view('todayprocesssteps', [
            'currentDate' => $currentDate,
            'processStats' => $processStats,
        ]);
    }
}
