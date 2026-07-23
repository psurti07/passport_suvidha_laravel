<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Customer;
use Yajra\DataTables\Facades\DataTables;

class LeadController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return view('admin.leads.index', compact('services'));
    }

    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = Customer::with('service')->select([
            'id',
            'service_id',
            'full_name',
            'email',
            'mobile_number',
            'is_paid',
            'created_at'
        ])->where('is_paid', 0);

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);
        }

        if ($request->service) {
            $query->where('service_id', $request->service);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('service_name', function ($row) {
                if (!$row->service) {
                    return '-';
                }
                $isTatkal = str_starts_with($row->service->service_code, 'TP');
                return '<span>
                    ' . ($isTatkal ? '🟢 ' : '⚪') . $row->service->service_name . '
                </span>';
            })

            ->addColumn('customer_name', function ($row) {
                return $row->full_name;
            })

            ->editColumn('is_paid', function ($row) {
                if ($row->is_paid == '0') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">Lead</span>';
                }
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y, h:i A');
            })

            ->addColumn('actions', function ($row) {
                return '
                    <form action="' . route('admin.customer.search') . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        <input type="hidden" name="mobile_no" value="' . $row->mobile_number . '">
                        <button type="submit" class="text-blue-600 hover:text-blue-900" title="View">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </form>
                ';
            })

            ->rawColumns(['service_name', 'is_paid', 'actions'])

            ->make(true);
    }

    public function today()
    {
        $services = Service::all();
        return view('admin.leads.today', compact('services'));
    }

    public function todayData(Request $request)
    {
        $query = Customer::select([
            'id',
            'service_id',
            'full_name',
            'email',
            'mobile_number',
            'is_paid',
            'created_at'
        ])->whereDate('created_at', now())->where('is_paid', 0);

        if ($request->service) {
            $query->where('service_id', $request->service);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('service_name', function ($row) {
                if (!$row->service) {
                    return '-';
                }
                $isTatkal = str_starts_with($row->service->service_code, 'TP');
                return '<span>
                            ' . ($isTatkal ? '🟢 ' : '⚪') . $row->service->service_name . '
                        </span>';
            })

            ->addColumn('customer_name', function ($row) {
                return $row->full_name;
            })

            ->editColumn('is_paid', function ($row) {
                if ($row->is_paid == '0') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">Lead</span>';
                }
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y, h:i A');
            })

            ->addColumn('actions', function ($row) {
                return '
                    <form action="' . route('admin.customer.search') . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        <input type="hidden" name="mobile_no" value="' . $row->mobile_number . '">
                        <button type="submit" class="text-blue-600 hover:text-blue-900" title="View">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </form>
                ';
            })

            ->rawColumns(['service_name', 'is_paid', 'actions'])

            ->make(true);
    }
}
