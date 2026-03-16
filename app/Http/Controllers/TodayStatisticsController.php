<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Carbon\Carbon;

class TodayStatisticsController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // Fetch OTP statistics for today
        $totalOtpToday = Otp::whereDate('created_at', $today)->count();
        $loginOtpToday = Otp::whereDate('created_at', $today)
            ->where('purpose', 'login')
            ->count();
        $registrationOtpToday = Otp::whereDate('created_at', $today)
            ->where('purpose', 'registration')
            ->count();
        
        
        
        // Fetch payment statistics for today
        $totalCustomersToday = Customer::whereDate('created_at', $today)->count();
        $paidCustomersToday = Customer::whereDate('created_at', $today)
            ->where('is_paid', true)
            ->count();
        $unpaidCustomersToday = Customer::whereDate('created_at', $today)
            ->where('is_paid', false)
            ->count();          
            
        $openTicketToday = Ticket::whereDate('created_at', $today)
            ->where('status', 1)
            ->count();           

        $todayStats = [           
            ['count' => $totalCustomersToday, 'label' => 'Total Customers Today', 'icon' => 'fa-users'],
            ['count' => $paidCustomersToday, 'label' => 'Paid Customers Today', 'icon' => 'fa-check-circle'],
            ['count' => $unpaidCustomersToday, 'label' => 'Unpaid Customers Today', 'icon' => 'fa-clock'],
            ['count' => $totalOtpToday, 'label' => 'Today OTP', 'icon' => 'fa-key'],
            ['count' => $loginOtpToday, 'label' => 'Portal Login - OTP', 'icon' => 'fa-sign-in-alt'],
            ['count' => $registrationOtpToday, 'label' => 'Passport Application - OTP', 'icon' => 'fa-passport'],
            ['count' => $openTicketToday, 'label' => 'Support Request - Open', 'icon' => 'fa-passport'],
        ];

        $currentDate = Date::now()->format('j M, Y'); // e.g., 20 Apr, 2025

        return view('todaystatistics', [
            'currentDate' => $currentDate,
            'todayStats' => $todayStats,
            // 'paymentStats' => $paymentStats,
        ]);
    }
} 