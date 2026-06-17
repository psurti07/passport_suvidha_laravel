<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
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

    public function customerReport(Request $request)
    {
        if ($request->ajax()) {

            $query = Customer::selectRaw("
                YEAR(created_at) as year,
                MONTH(created_at) as month_no,
                MONTHNAME(created_at) as month_name,
                COUNT(*) as total
            ")
                ->where('is_paid', 1)
                ->groupByRaw("
                YEAR(created_at),
                MONTH(created_at),
                MONTHNAME(created_at)
            ");

            $grandTotal = Customer::where('is_paid', 1)->count();

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

    public function leadMonthDetails(Request $request)
    {
        $year = $request->year;
        $month = $request->month;

        $start = Carbon::create($year, $month, 1);

        $end = $start->copy()->endOfMonth();

        // 🚀 limit future dates
        $end = $end->gt(now()) ? now() : $end;

        // get grouped leads
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

    public function customerMonthDetails(Request $request)
    {
        $year = $request->year;
        $month = $request->month;

        $start = Carbon::create($year, $month, 1);

        $end = $start->copy()->endOfMonth();

        $end = $end->gt(now()) ? now() : $end;

        // get grouped leads
        $leads = Customer::selectRaw("DATE(created_at) as date, COUNT(*) as total")
            ->where('is_paid', 1)
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

    public function serviceReport(Request $request)
    {
        if ($request->ajax()) {

            $query = Customer::query()
                ->join('services', 'services.id', '=', 'customers.service_id')
                ->selectRaw("
                YEAR(customers.created_at) as year,
                MONTH(customers.created_at) as month_no,
                MONTHNAME(customers.created_at) as month_name,

                SUM(CASE WHEN services.service_code = 'NP36' THEN 1 ELSE 0 END) as np36,
                SUM(CASE WHEN services.service_code = 'NP60' THEN 1 ELSE 0 END) as np60,
                SUM(CASE WHEN services.service_code = 'TP36' THEN 1 ELSE 0 END) as tp36,
                SUM(CASE WHEN services.service_code = 'TP60' THEN 1 ELSE 0 END) as tp60
            ")
                ->groupByRaw("
                YEAR(customers.created_at),
                MONTH(customers.created_at),
                MONTHNAME(customers.created_at)
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

        $rawData = Customer::query()
            ->join('services', 'services.id', '=', 'customers.service_id')
            ->selectRaw("
            DATE(customers.created_at) as report_date,

            SUM(CASE WHEN services.service_code = 'NP36' THEN 1 ELSE 0 END) as np36,
            SUM(CASE WHEN services.service_code = 'NP60' THEN 1 ELSE 0 END) as np60,
            SUM(CASE WHEN services.service_code = 'TP36' THEN 1 ELSE 0 END) as tp36,
            SUM(CASE WHEN services.service_code = 'TP60' THEN 1 ELSE 0 END) as tp60
        ")
            ->whereBetween('customers.created_at', [$start, $end])
            ->groupByRaw("DATE(customers.created_at)")
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
}
