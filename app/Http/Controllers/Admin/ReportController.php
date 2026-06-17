<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

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
}
