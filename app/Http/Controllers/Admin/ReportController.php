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
use App\Models\ApplicationStatus;

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

            $baseQuery = Customer::query()
                ->join('services', 'services.id', '=', 'customers.service_id')
                ->selectRaw("
                customers.id,
                services.service_code,

                CASE
                    WHEN customers.is_paid = 1
                         AND customers.payment_date IS NOT NULL
                    THEN customers.payment_date
                    ELSE customers.created_at
                END AS report_date
            ");

            $query = DB::query()
                ->fromSub($baseQuery, 'report_data')
                ->selectRaw("
                YEAR(report_date) AS year,
                MONTH(report_date) AS month_no,
                MONTHNAME(report_date) AS month_name,

                SUM(
                    CASE
                        WHEN service_code = 'NP36'
                        THEN 1
                        ELSE 0
                    END
                ) AS np36,

                SUM(
                    CASE
                        WHEN service_code = 'NP60'
                        THEN 1
                        ELSE 0
                    END
                ) AS np60,

                SUM(
                    CASE
                        WHEN service_code = 'TP36'
                        THEN 1
                        ELSE 0
                    END
                ) AS tp36,

                SUM(
                    CASE
                        WHEN service_code = 'TP60'
                        THEN 1
                        ELSE 0
                    END
                ) AS tp60
            ")
                ->groupByRaw("
                YEAR(report_date),
                MONTH(report_date),
                MONTHNAME(report_date)
            ");

            return DataTables::of($query)
                ->addColumn('datewise', function ($row) {

                    return '
                    <button
                        type="button"
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
        $start = Carbon::create(
            $request->year,
            $request->month,
            1
        )->startOfMonth();

        $end = Carbon::create(
            $request->year,
            $request->month,
            1
        )->endOfMonth();

        // Do not allow future dates
        if ($end->gt(now())) {
            $end = now();
        }

        $baseQuery = Customer::query()
            ->join('services', 'services.id', '=', 'customers.service_id')
            ->selectRaw("
            customers.id,
            services.service_code,

            CASE
                WHEN customers.is_paid = 1
                     AND customers.payment_date IS NOT NULL
                THEN customers.payment_date
                ELSE customers.created_at
            END AS report_date
        ");

        $rawData = DB::query()
            ->fromSub($baseQuery, 'report_data')
            ->selectRaw("
            DATE(report_date) AS report_date,

            SUM(
                CASE
                    WHEN service_code = 'NP36'
                    THEN 1
                    ELSE 0
                END
            ) AS np36,

            SUM(
                CASE
                    WHEN service_code = 'NP60'
                    THEN 1
                    ELSE 0
                END
            ) AS np60,

            SUM(
                CASE
                    WHEN service_code = 'TP36'
                    THEN 1
                    ELSE 0
                END
            ) AS tp36,

            SUM(
                CASE
                    WHEN service_code = 'TP60'
                    THEN 1
                    ELSE 0
                END
            ) AS tp60
        ")
            ->whereBetween('report_date', [$start, $end])
            ->groupByRaw('DATE(report_date)')
            ->get()
            ->keyBy('report_date');

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

            $np36 = (int) ($row->np36 ?? 0);
            $np60 = (int) ($row->np60 ?? 0);
            $tp36 = (int) ($row->tp36 ?? 0);
            $tp60 = (int) ($row->tp60 ?? 0);

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
            'grand_total' => $grand,
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
    public function applicationStatusReport(Request $request)
    {
        if ($request->ajax()) {

            $statusSlugs = [
                'in_process',
                'appointment_scheduled',
                'pov_success',
                'pov_failed',
                'pov_insufficient_documents',
            ];

            $latestProgress = DB::table('application_progress as ap')
                ->whereNull('ap.deleted_at')

                ->whereNotExists(function ($subQuery) {

                    $subQuery->select(DB::raw(1))
                        ->from('application_progress as newer')
                        ->whereColumn(
                            'newer.customer_id',
                            'ap.customer_id'
                        )
                        ->whereNull('newer.deleted_at')

                        ->where(function ($query) {

                            $query->whereColumn(
                                'newer.status_date',
                                '>',
                                'ap.status_date'
                            )

                                ->orWhere(function ($query) {

                                    $query->whereColumn(
                                        'newer.status_date',
                                        'ap.status_date'
                                    )
                                        ->whereColumn(
                                            'newer.id',
                                            '>',
                                            'ap.id'
                                        );
                                });
                        });
                });

            $baseQuery = $latestProgress
                ->join(
                    'application_statuses',
                    'application_statuses.id',
                    '=',
                    'ap.status_id'
                )

                ->whereIn(
                    'application_statuses.slug',
                    $statusSlugs
                )

                ->select([
                    'ap.id',
                    'ap.customer_id',
                    'ap.status_id',
                    'ap.status_date',
                    'application_statuses.slug',
                    'application_statuses.status_name',
                ]);

            $query = DB::query()
                ->fromSub($baseQuery, 'report_data')

                ->selectRaw("
                YEAR(status_date) AS year,

                MONTH(status_date) AS month_no,

                MONTHNAME(status_date) AS month_name,

                SUM(
                    CASE
                        WHEN slug = 'in_process'
                        THEN 1
                        ELSE 0
                    END
                ) AS in_process,

                SUM(
                    CASE
                        WHEN slug = 'appointment_scheduled'
                        THEN 1
                        ELSE 0
                    END
                ) AS appointment_scheduled,

                SUM(
                    CASE
                        WHEN slug = 'pov_success'
                        THEN 1
                        ELSE 0
                    END
                ) AS payment_success,

                SUM(
                    CASE
                        WHEN slug = 'pov_failed'
                        THEN 1
                        ELSE 0
                    END
                ) AS payment_failed,

                SUM(
                    CASE
                        WHEN slug = 'pov_insufficient_documents'
                        THEN 1
                        ELSE 0
                    END
                ) AS insufficient_documents
            ")

                ->groupByRaw("
                YEAR(status_date),
                MONTH(status_date),
                MONTHNAME(status_date)
            ")

                ->orderByRaw("
                YEAR(status_date) DESC,
                MONTH(status_date) DESC
            ");

            $grandTotal = DB::query()
                ->fromSub($baseQuery, 'latest_status')

                ->selectRaw("
                SUM(
                    CASE
                        WHEN slug = 'in_process'
                        THEN 1
                        ELSE 0
                    END
                ) AS in_process,

                SUM(
                    CASE
                        WHEN slug = 'appointment_scheduled'
                        THEN 1
                        ELSE 0
                    END
                ) AS appointment_scheduled,

                SUM(
                    CASE
                        WHEN slug = 'pov_success'
                        THEN 1
                        ELSE 0
                    END
                ) AS payment_success,

                SUM(
                    CASE
                        WHEN slug = 'pov_failed'
                        THEN 1
                        ELSE 0
                    END
                ) AS payment_failed,

                SUM(
                    CASE
                        WHEN slug = 'pov_insufficient_documents'
                        THEN 1
                        ELSE 0
                    END
                ) AS insufficient_documents
            ")

                ->first();

            $dataTable = DataTables::of($query)

                ->addColumn('datewise', function ($row) {

                    return '
                    <button
                        type="button"
                        class="view-month px-4 py-1 text-sm font-medium
                               text-blue-600 border border-blue-600
                               rounded-lg hover:bg-blue-600
                               hover:text-white transition"
                        data-year="' . $row->year . '"
                        data-month="' . $row->month_no . '">

                        View Datewise

                    </button>
                ';
                })

                ->rawColumns(['datewise'])

                ->make(true);

            $json = $dataTable->getData(true);

            $json['grand_total'] = [

                'in_process' =>
                (int) ($grandTotal->in_process ?? 0),

                'appointment_scheduled' =>
                (int) ($grandTotal->appointment_scheduled ?? 0),

                'payment_success' =>
                (int) ($grandTotal->payment_success ?? 0),

                'payment_failed' =>
                (int) ($grandTotal->payment_failed ?? 0),

                'insufficient_documents' =>
                (int) ($grandTotal->insufficient_documents ?? 0),

            ];

            return response()->json($json);
        }

        return view(
            'admin.reports.application_status_report'
        );
    }

    public function applicationStatusReportMonthDetails(Request $request)
    {
        $year = $request->year;
        $month = $request->month;

        $start = Carbon::create($year, $month, 1);

        $end = $start->copy()->endOfMonth();

        // Prevent future dates
        if ($end->gt(now())) {
            $end = now();
        }

        $statusSlugs = [
            'in_process',
            'appointment_scheduled',
            'pov_success',
            'pov_failed',
            'pov_insufficient_documents',
        ];

        $latestProgress = DB::table('application_progress as ap')

            ->whereNull('ap.deleted_at')

            ->whereNotExists(function ($subQuery) {

                $subQuery->select(DB::raw(1))

                    ->from('application_progress as newer')

                    ->whereColumn(
                        'newer.customer_id',
                        'ap.customer_id'
                    )

                    ->whereNull('newer.deleted_at')

                    ->where(function ($query) {

                        $query->whereColumn(
                            'newer.status_date',
                            '>',
                            'ap.status_date'
                        )

                            ->orWhere(function ($query) {

                                $query->whereColumn(
                                    'newer.status_date',
                                    'ap.status_date'
                                )

                                    ->whereColumn(
                                        'newer.id',
                                        '>',
                                        'ap.id'
                                    );
                            });
                    });
            });

        $rawData = $latestProgress

            ->join(
                'application_statuses',
                'application_statuses.id',
                '=',
                'ap.status_id'
            )

            ->selectRaw("
            DATE(ap.status_date) AS report_date,

            SUM(
                CASE
                    WHEN application_statuses.slug = 'in_process'
                    THEN 1
                    ELSE 0
                END
            ) AS in_process,

            SUM(
                CASE
                    WHEN application_statuses.slug = 'appointment_scheduled'
                    THEN 1
                    ELSE 0
                END
            ) AS appointment_scheduled,

            SUM(
                CASE
                    WHEN application_statuses.slug = 'pov_success'
                    THEN 1
                    ELSE 0
                END
            ) AS payment_success,

            SUM(
                CASE
                    WHEN application_statuses.slug = 'pov_failed'
                    THEN 1
                    ELSE 0
                END
            ) AS payment_failed,

            SUM(
                CASE
                    WHEN application_statuses.slug = 'pov_insufficient_documents'
                    THEN 1
                    ELSE 0
                END
            ) AS insufficient_documents
        ")

            ->whereIn(
                'application_statuses.slug',
                $statusSlugs
            )

            ->whereBetween(
                'ap.status_date',
                [$start, $end]
            )

            ->groupByRaw(
                'DATE(ap.status_date)'
            )

            ->get()

            ->keyBy('report_date');

        $period = CarbonPeriod::create(
            $start,
            $end
        );

        $data = [];

        $grand = [
            'in_process' => 0,
            'appointment_scheduled' => 0,
            'payment_success' => 0,
            'payment_failed' => 0,
            'insufficient_documents' => 0,
        ];

        foreach ($period as $date) {

            $key = $date->format('Y-m-d');

            $row = $rawData[$key] ?? null;


            $inProcess = (int) (
                $row->in_process ?? 0
            );

            $appointmentScheduled = (int) (
                $row->appointment_scheduled ?? 0
            );

            $paymentSuccess = (int) (
                $row->payment_success ?? 0
            );

            $paymentFailed = (int) (
                $row->payment_failed ?? 0
            );

            $insufficientDocuments = (int) (
                $row->insufficient_documents ?? 0
            );

            $data[] = [
                'report_date' => $key,
                'in_process' => $inProcess,
                'appointment_scheduled' => $appointmentScheduled,
                'payment_success' => $paymentSuccess,
                'payment_failed' => $paymentFailed,
                'insufficient_documents' => $insufficientDocuments,
            ];

            $grand['in_process'] += $inProcess;
            $grand['appointment_scheduled'] += $appointmentScheduled;
            $grand['payment_success'] += $paymentSuccess;
            $grand['payment_failed'] += $paymentFailed;
            $grand['insufficient_documents'] += $insufficientDocuments;
        }

        return response()->json([
            'data' => $data,
            'grand_total' => $grand,
        ]);
    }
}
