<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApplicationProgress;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ApplicationStatus;
use Illuminate\Support\Str;

class ApplicationStatusController extends Controller
{
    public function index()
    {
        $statuses = ApplicationStatus::orderBy('priority_no')->get();
        return view('admin.application-status.index', compact('statuses'));
    }

    public function verification()
    {
        $statuses = ApplicationStatus::orderBy('priority_no')->get();

        return view('admin.application-status.index', [
            'statuses' => $statuses,
            'type' => 'verification',
            'title' => 'VERIFICATION'
        ]);
    }

    public function appointment()
    {
        $statuses = ApplicationStatus::orderBy('priority_no')->get();

        return view('admin.application-status.index', [
            'statuses' => $statuses,
            'type' => 'appointment',
            'title' => 'APPOINTMENT'
        ]);
    }

    public function success()
    {
        $statuses = ApplicationStatus::orderBy('priority_no')->get();

        return view('admin.application-status.index', [
            'statuses' => $statuses,
            'type' => 'success',
            'title' => 'SUCCESS'
        ]);
    }

    public function failed()
    {
        $statuses = ApplicationStatus::orderBy('priority_no')->get();

        return view('admin.application-status.index', [
            'statuses' => $statuses,
            'type' => 'failed',
            'title' => 'FAILED'
        ]);
    }

    public function insufficient()
    {
        $statuses = ApplicationStatus::orderBy('priority_no')->get();

        return view('admin.application-status.index', [
            'statuses' => $statuses,
            'type' => 'insufficient',
            'title' => 'INSUFFICIENT'
        ]);
    }

    public function refund()
    {
        $statuses = ApplicationStatus::orderBy('priority_no')->get();

        return view('admin.application-status.index', [
            'statuses' => $statuses,
            'type' => 'refund',
            'title' => 'REFUND'
        ]);
    }

    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = ApplicationProgress::with(['customer', 'status', 'remarkedByUser'])
            ->latest('status_date');

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('status_date', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);
        }

        if ($request->status) {
            $query->where('status_id', $request->status);
        }

        if ($request->type == 'verification') {
            $query->whereHas('status', function ($q) {
                $q->where('slug', 'verification_ohk');
            });
        }

        if ($request->type == 'appointment') {
            $query->whereHas('status', function ($q) {
                $q->where('slug', 'appointment_scheduled');
            });
        }

        if ($request->type == 'success') {
            $query->whereHas('status', function ($q) {
                $q->where('slug', 'pov_success');
            });
        }

        if ($request->type == 'failed') {
            $query->whereHas('status', function ($q) {
                $q->where('slug', 'pov_failed');
            });
        }

        if ($request->type == 'insufficient') {
            $query->whereHas('status', function ($q) {
                $q->where('slug', 'pov_insufficient_documents');
            });
        }

        if ($request->type == 'refund') {
            $query->whereHas('status', function ($q) {
                $q->where('slug', 'refunded');
            });
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('customer_name', function ($row) {
                return Str::title(strtolower($row->customer->full_name));
            })

            ->addColumn('customer_mobile', function ($row) {
                return $row->customer->mobile_number;
            })

            ->addColumn('service_name', function ($row) {
                return $row->customer->service->service_name ?? 'N/A';
            })

            ->addColumn('status_name', function ($row) {

                $status = $row->status->status_name ?? 'N/A';
                $color = $row->status->colorclass ?? 'gray';

                return '<span class="px-2 py-1 text-xs rounded bg-' . $color . '-100 text-' . $color . '-800">'
                    . $status .
                    '</span>';
            })

            ->addColumn('remark', function ($row) {
                return $row->remark;
            })

            ->addColumn('user_remarked_by', function ($row) {
                return $row->remarkedByUser->name ?? 'N/A';
            })

            ->editColumn('status_date', function ($row) {
                return $row->status_date->format('d M Y');
            })

            ->filterColumn('customer_name', function ($query, $keyword) {
                $query->whereHas('customer', function ($q) use ($keyword) {
                    $q->where('full_name', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('customer_mobile', function ($query, $keyword) {
                $query->whereHas('customer', function ($q) use ($keyword) {
                    $q->where('mobile_number', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('service_name', function ($query, $keyword) {
                $query->whereHas('customer.service', function ($q) use ($keyword) {
                    $q->where('service_name', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('remark', function ($query, $keyword) {
                $query->where('remark', 'like', "%{$keyword}%");
            })

            ->filterColumn('user_remarked_by', function ($query, $keyword) {
                $query->whereHas('remarkedByUser', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('actions', function ($row) {
                return '
                    <a href="' . route('admin.customers.show', $row->customer->id) . '#application-process" 
                    class="group inline-flex items-center justify-center h-9 w-9 rounded-lg border border-blue-200 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:border-blue-600 transition-all duration-200 shadow-sm" 
                    title="View">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                    </a>
                ';
            })

            ->rawColumns(['status_name', 'actions'])

            ->make(true);
    }
}
