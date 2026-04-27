<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TodayStatisticsController extends Controller
{
    public function index()
    {
        $yesterday = Carbon::yesterday();
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        // ================= CUSTOMER =================
        $totalCustomersToday = Customer::whereDate('created_at', $today)->count();

        $leadCustomersToday = Customer::whereDate('created_at', $today)
            ->where('is_paid', false)
            ->count();

        $paidCustomersToday = Customer::whereDate('created_at', $today)
            ->where('is_paid', true)
            ->count();

        $serviceCounts = DB::table('customers')
            ->join('services', 'customers.service_id', '=', 'services.id')
            ->whereDate('customers.created_at', $today)
            ->select('services.service_code', DB::raw('COUNT(*) as total'))
            ->groupBy('services.service_code')
            ->get();

        $counts = [
            'NP36' => 0,
            'NP60' => 0,
            'TP36' => 0,
            'TP60' => 0,
        ];

        foreach ($serviceCounts as $item) {
            $counts[$item->service_code] = $item->total;
        }

        $normal36 = $counts['NP36'];
        $normal60 = $counts['NP60'];
        $tatkal36 = $counts['TP36'];
        $tatkal60 = $counts['TP60'];

        // ================= APPOINTMENT =================
        $yesterdayAppointments = DB::table('appointment_letters')
            ->whereDate('appointment_date', $yesterday)
            ->count();

        $todayAppointments = DB::table('appointment_letters')
            ->whereDate('appointment_date', $today)
            ->count();

        $tomorrowAppointments = DB::table('appointment_letters')
            ->whereDate('appointment_date', $tomorrow)
            ->count();


        // ================= OTP =================
        $totalOtpToday = Otp::whereDate('created_at', $today)->count();

        $loginOtpToday = Otp::whereDate('created_at', $today)
            ->where('purpose', 'login')
            ->count();

        $registrationOtpToday = Otp::whereDate('created_at', $today)
            ->where('purpose', 'registration')
            ->count();

        // ================= TICKETS =================
        $openTicketToday = Ticket::whereDate('created_at', $today)
            ->where('status', 'open') // FIX (enum chhe)
            ->count();

        // ================= PAYMENT STATS =================

        // Razorpay → Services
        $razorpayServices = DB::table('razorpay_logs_entry')
            ->select('service_type', DB::raw('COUNT(*) as total'))
            ->whereDate('created_at', $today)
            ->where('tx_status', 'success')
            ->whereNotNull('service_type')
            ->groupBy('service_type')
            ->get()
            ->map(
                function ($item) {
                    return [
                        'label' => 'Razorpay - ' . $item->service_type,
                        'count' => $item->total,
                        'icon' => 'fa-credit-card'
                    ];
                }
            );

        // Cashfree → Card Offer
        $cashfreeCardOffer = DB::table('cashfree_logs_entry')
            ->whereDate('created_at', $today)
            ->where('tx_status', 'success')
            ->where('offer_type', 1)
            ->count();

        // Zaakpay → Star Offer
        $zaakpayStarOffer = DB::table('zaakpay_logs_entry')
            ->whereDate('created_at', $today)
            ->where('tx_status', 'success')
            ->where('offer_type', 2)
            ->count();

        // Convert to array format
        $paymentStats = $razorpayServices->toArray();

        // Add Card Offer
        $paymentStats[] = [
            'label' => 'Cashfree - Card Offer',
            'count' => $cashfreeCardOffer,
            'icon' => 'fa-gift'
        ];

        // Add Star Offer
        $paymentStats[] = [
            'label' => 'Zaakpay - Star Offer',
            'count' => $zaakpayStarOffer,
            'icon' => 'fa-star'
        ];

        // ================= MAIN STATS =================
        $customerStats = [
            ['count' => $totalCustomersToday, 'label' => 'Total Customers', 'icon' => 'fa-users'],
            ['count' => $leadCustomersToday, 'label' => 'Lead Customers', 'icon' => 'fa-clock'],
            ['count' => $paidCustomersToday, 'label' => 'Paid Customers', 'icon' => 'fa-check-circle'],

            ['count' => $normal36, 'label' => 'Normal - 36 Pages', 'icon' => 'fa-id-card'],
            ['count' => $normal60, 'label' => 'Normal - 60 Pages', 'icon' => 'fa-id-card'],
            ['count' => $tatkal36, 'label' => 'Tatkal - 36 Pages', 'icon' => 'fa-bolt'],
            ['count' => $tatkal60, 'label' => 'Tatkal - 60 Pages', 'icon' => 'fa-bolt']
        ];

        $appointmentStats = [
            ['count' => $yesterdayAppointments, 'label' => 'Yesterday Appointment', 'icon' => 'fa-calendar-minus'],
            ['count' => $todayAppointments, 'label' => 'Today Appointment', 'icon' => 'fa-calendar-day'],
            ['count' => $tomorrowAppointments, 'label' => 'Tomorrow Appointment', 'icon' => 'fa-calendar-plus'],
        ];

        $otpStats = [
            ['count' => $totalOtpToday, 'label' => 'Today OTP', 'icon' => 'fa-key'],
            ['count' => $loginOtpToday, 'label' => 'Portal Login - OTP', 'icon' => 'fa-sign-in-alt'],
            ['count' => $registrationOtpToday, 'label' => 'Passport Application - OTP', 'icon' => 'fa-passport']
        ];

        $ticketStats = [
            ['count' => $openTicketToday, 'label' => 'Support Request - Open', 'icon' => 'fa-headset'],
        ];

        $currentDate = Date::now()->format('j M, Y');

        return view('todaystatistics', [
            'currentDate' => $currentDate,
            'customerStats' => $customerStats,
            'appointmentStats' => $appointmentStats,
            'otpStats' => $otpStats,
            'ticketStats' => $ticketStats,
            'paymentStats' => $paymentStats
        ]);
    }
}
