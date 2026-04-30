<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApplicationProgress;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ApplicationStatus;

class ApplicationStatusController extends Controller
{
    public function index()
    {
        $statuses = ApplicationStatus::orderBy('priority_no')->get();
        return view('admin.application-status.index', compact('statuses'));
    }

    public function new()
    {
        $statuses = ApplicationStatus::orderBy('priority_no')->get();

        return view('admin.application-status.index', [
            'statuses' => $statuses,
            'type' => 'new',
            'title' => 'NEW'
        ]);
    }

    public function current()
    {
        $statuses = ApplicationStatus::orderBy('priority_no')->get();

        return view('admin.application-status.index', [
            'statuses' => $statuses,
            'type' => 'current',
            'title' => 'CURRENT'
        ]);
    }

    public function completed()
    {
        $statuses = ApplicationStatus::orderBy('priority_no')->get();

        return view('admin.application-status.index', [
            'statuses' => $statuses,
            'type' => 'completed',
            'title' => 'COMPLETED'
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

        if ($request->type == 'new') {
            $query->whereHas('status', function ($q) {
                $q->where('status_name', 'Documents Submitted');
            });
        }

        if ($request->type == 'current') {
            $query->whereHas('status', function ($q) {
                $q->whereIn('status_name', [
                    'In Process',
                    'Details Verification',
                    'Appointment Scheduled',
                    'Appointment Rescheduled 1',
                    'Appointment Rescheduled 2',
                    'Appointment Rescheduled 3'
                ]);
            });
        }

        if ($request->type == 'completed') {
            $query->whereHas('status', function ($q) {
                $q->whereIn('status_name', [
                    'POV Success',
                    'POV Failed',
                    'POV Insufficient Documents'
                ]);
            });
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('customer_name', function ($row) {
                return $row->customer->first_name . ' ' . $row->customer->last_name;
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
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%");
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
                    class="text-blue-600 hover:text-blue-900" 
                    title="View">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd"
                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                ';
            })

            ->rawColumns(['status_name', 'actions'])

            ->make(true);
    }
}
