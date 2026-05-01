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
            'first_name',
            'last_name',
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
                return $row->first_name . ' ' . $row->last_name;
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
                                viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </form>
                ';
            })

            ->rawColumns(['service_name', 'is_paid', 'actions'])

            ->make(true);
    }

    /**
     * Display a listing of today's customers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
            'first_name',
            'last_name',
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
                return $row->first_name . ' ' . $row->last_name;
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
                                viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </form>
                ';
            })

            ->rawColumns(['service_name', 'is_paid', 'actions'])

            ->make(true);
    }
}
