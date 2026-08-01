<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;


class ReportController extends Controller
{
    public function leadReport(Request $request)
    {
        if ($request->ajax()) {

            $query = Customer::selectRaw("
                YEAR(created_at) as year,
                MONTH(created_at) as month_no,
                MONTHNAME(created_at) as month_name,
                COUNT(*) as total
            ")
                ->where('is_paid', 0)
                ->groupByRaw("
                YEAR(created_at),
                MONTH(created_at),
                MONTHNAME(created_at)
            ");

            $grandTotal = Customer::where('is_paid', 0)->count();

            return DataTables::of($query)
                ->editColumn('datewise_date', function ($row) {
                    return $row->year . '-' . str_pad($row->month_no, 2, '0', STR_PAD_LEFT);
                })
                ->with([
                    'grand_total' => $grandTotal
                ])
                ->make(true);
        }

        return view('admin.reports.lead_report');
    }

    public function leadMonthDetails(Request $request)
    {
        $year = $request->year;
        $month = $request->month;

        $start = Carbon::create($year, $month, 1);

        $end = $start->copy()->endOfMonth();

        $end = $end->gt(now()) ? now() : $end;

        $leads = Customer::selectRaw("DATE(created_at) as date, COUNT(*) as total")
            ->where('is_paid', 0)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->pluck('total', 'date');

        $data = [];

        for ($date = $start; $date->lte($end); $date->addDay()) {
            $day = $date->format('Y-m-d');

            $data[] = [
                'date' => $day,
                'total' => $leads[$day] ?? 0
            ];
        }

        return response()->json([
            'data' => $data,
            'grand_total' => array_sum(array_column($data, 'total'))
        ]);
    }

    public function customerReport(Request $request)
    {
        if ($request->ajax()) {

            $query = Customer::selectRaw("
                YEAR(payment_date) as year,
                MONTH(payment_date) as month_no,
                MONTHNAME(payment_date) as month_name,
                COUNT(*) as total
            ")
                ->where('is_paid', 1)
                ->whereNotNull('payment_date')
                ->groupByRaw("
                YEAR(payment_date),
                MONTH(payment_date),
                MONTHNAME(payment_date)
            ");

            $grandTotal = Customer::where('is_paid', 1)
                ->whereNotNull('payment_date')
                ->count();

            return DataTables::of($query)
                ->editColumn('datewise_date', function ($row) {
                    return $row->year . '-' . str_pad($row->month_no, 2, '0', STR_PAD_LEFT);
                })
                ->with([
                    'grand_total' => $grandTotal
                ])
                ->make(true);
        }

        return view('admin.reports.customer_report');
    }

    public function customerMonthDetails(Request $request)
    {
        $year = $request->year;
        $month = $request->month;

        $start = Carbon::create($year, $month, 1);

        $end = $start->copy()->endOfMonth();

        $end = $end->gt(now()) ? now() : $end;

        // get grouped leads
        $leads = Customer::selectRaw("DATE(payment_date) as date, COUNT(*) as total")
            ->where('is_paid', 1)
            ->whereBetween('payment_date', [$start, $end])
            ->groupBy('date')
            ->pluck('total', 'date');

        $data = [];

        for ($date = $start; $date->lte($end); $date->addDay()) {
            $day = $date->format('Y-m-d');

            $data[] = [
                'date' => $day,
                'total' => $leads[$day] ?? 0
            ];
        }

        return response()->json([
            'data' => $data,
            'grand_total' => array_sum(array_column($data, 'total'))
        ]);
    }

    public function serviceReport(Request $request)
    {
        if ($request->ajax()) {

            $dateColumn = "
            CASE
                WHEN customers.is_paid = 1 AND customers.payment_date IS NOT NULL THEN customers.payment_date
                ELSE customers.created_at
            END
        ";

            $query = Customer::query()
                ->join('services', 'services.id', '=', 'customers.service_id')
                ->selectRaw("
                YEAR($dateColumn) as year,
                MONTH($dateColumn) as month_no,
                MONTHNAME($dateColumn) as month_name,

                SUM(CASE WHEN services.service_code = 'NP36' THEN 1 ELSE 0 END) as np36,
                SUM(CASE WHEN services.service_code = 'NP60' THEN 1 ELSE 0 END) as np60,
                SUM(CASE WHEN services.service_code = 'TP36' THEN 1 ELSE 0 END) as tp36,
                SUM(CASE WHEN services.service_code = 'TP60' THEN 1 ELSE 0 END) as tp60
            ")
                ->groupByRaw("
                YEAR($dateColumn),
                MONTH($dateColumn),
                MONTHNAME($dateColumn)
            ");

            return DataTables::of($query)
                ->addColumn('datewise', function ($row) {
                    return '
                    <button
                        class="view-month px-4 py-1 text-sm font-medium text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition"
                        data-year="' . $row->year . '"
                        data-month="' . $row->month_no . '">
                        View Datewise
                    </button>
                ';
                })
                ->rawColumns(['datewise'])
                ->make(true);
        }

        return view('admin.reports.service_report');
    }
    public function serviceReportMonthDetails(Request $request)
    {
        $start = Carbon::create($request->year, $request->month, 1)->startOfMonth();
        $end = Carbon::create($request->year, $request->month, 1)->endOfMonth();

        // ❌ no future dates allowed
        if ($end->gt(now())) {
            $end = now();
        }

        $dateColumn = "
            CASE
                WHEN customers.is_paid = 1 AND customers.payment_date IS NOT NULL THEN customers.payment_date
                ELSE customers.created_at
            END";

        $rawData = Customer::query()
            ->join('services', 'services.id', '=', 'customers.service_id')
            ->selectRaw("
            DATE($dateColumn) as report_date,

            SUM(CASE WHEN services.service_code = 'NP36' THEN 1 ELSE 0 END) as np36,
            SUM(CASE WHEN services.service_code = 'NP60' THEN 1 ELSE 0 END) as np60,
            SUM(CASE WHEN services.service_code = 'TP36' THEN 1 ELSE 0 END) as tp36,
            SUM(CASE WHEN services.service_code = 'TP60' THEN 1 ELSE 0 END) as tp60
        ")
            ->whereBetween(DB::raw($dateColumn), [$start, $end])
            ->groupByRaw("DATE($dateColumn)")
            ->get()
            ->keyBy('report_date');

        // Generate full date range
        $period = CarbonPeriod::create($start, $end);

        $data = [];
        $grand = [
            'np36' => 0,
            'np60' => 0,
            'tp36' => 0,
            'tp60' => 0,
        ];

        foreach ($period as $date) {

            $key = $date->format('Y-m-d');

            $row = $rawData[$key] ?? null;

            $np36 = $row->np36 ?? 0;
            $np60 = $row->np60 ?? 0;
            $tp36 = $row->tp36 ?? 0;
            $tp60 = $row->tp60 ?? 0;

            $data[] = [
                'report_date' => $key,
                'np36' => $np36,
                'np60' => $np60,
                'tp36' => $tp36,
                'tp60' => $tp60,
            ];

            $grand['np36'] += $np36;
            $grand['np60'] += $np60;
            $grand['tp36'] += $tp36;
            $grand['tp60'] += $tp60;
        }

        return response()->json([
            'data' => $data,
            'grand_total' => $grand
        ]);
    }


    public function invoiceReport(Request $request)
    {
        if ($request->ajax()) {

            $query = Invoice::selectRaw("
                YEAR(inv_date) as year,
                MONTH(inv_date) as month_no,
                MONTHNAME(inv_date) as month_name,
                SUM(total_amount) as total_amount
            ")
                ->whereNull('deleted_at')
                ->groupByRaw("
                YEAR(inv_date),
                MONTH(inv_date),
                MONTHNAME(inv_date)
            ");

            $grandTotal = Invoice::whereNull('deleted_at')
                ->sum('total_amount');

            return DataTables::of($query)
                ->with([
                    'grand_total' => number_format($grandTotal, 2, '.', '')
                ])
                ->make(true);
        }

        return view('admin.reports.invoice_report');
    }

    public function invoiceReportMonthDetails(Request $request)
    {
        $year = $request->year;
        $month = $request->month;

        $start = Carbon::create($year, $month, 1);

        $end = $start->copy()->endOfMonth();

        // Prevent future dates
        if ($end->gt(now())) {
            $end = now();
        }

        $invoices = Invoice::selectRaw("
            DATE(inv_date) as date,
            SUM(total_amount) as total_amount
        ")
            ->whereNull('deleted_at')
            ->whereBetween('inv_date', [$start, $end])
            ->groupBy('date')
            ->pluck('total_amount', 'date');

        $data = [];
        $grandTotal = 0;

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {

            $day = $date->format('Y-m-d');

            $amount = $invoices[$day] ?? 0;

            $grandTotal += $amount;

            $data[] = [
                'date' => $day,
                'total_amount' => number_format($amount, 2, '.', '')
            ];
        }

        return response()->json([
            'data' => $data,
            'grand_total' => number_format($grandTotal, 2, '.', '')
        ]);
    }
}
