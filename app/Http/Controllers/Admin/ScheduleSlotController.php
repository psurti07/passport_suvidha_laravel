<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScheduleSlot;
use Yajra\DataTables\Facades\DataTables;

class ScheduleSlotController extends Controller
{
    public function index()
    {
        return view('admin.schedule-slots.index');
    }

    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = ScheduleSlot::with('customer')->select([
            'id',
            'customer_id',
            'service_id',
            'date',
            'time',
            'language',
            'remarks',
            'status',
            'created_at',
            'updated_at',
            'deleted_at',
        ])

            ->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);

        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('customer_name', function ($row) {
                return $row->customer->first_name . ' ' . $row->customer->last_name ?? 'N/A';
            })

            ->addColumn('customer_mobile', function ($row) {
                return $row->customer->mobile_number ?? 'N/A';
            })

            ->addColumn('service_name', function ($row) {
                return $row->customer->service->service_name ?? 'N/A';
            })

            ->addColumn('date_time', function ($row) {
                return $row->date->format('d M Y') . ' ' . $row->time->format('h:i A');
            })

            ->editColumn('language', function ($row) {
                if ($row->language == '1') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-800">Hindi</span>';
                } else if ($row->language == '2') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">English</span>';
                } else if ($row->language == '3') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-red-100 text-red-800">Gujarati</span>';
                } else {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-800">Unknown</span>';
                }
            })

            ->editColumn('status', function ($row) {
                if ($row->status == '1') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-800">Scheduled</span>';
                } else if ($row->status == '2') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">Completed</span>';
                } else if ($row->status == '3') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-red-100 text-red-800">Cancelled</span>';
                } else if ($row->status == '4') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">Not Reachable</span>';
                } else {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-800">Unknown</span>';
                }
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

            ->addColumn('actions', function ($row) {
                return '
                    <div class="flex items-center gap-2">

                        <!-- View -->
                        <a href="' . route('admin.schedule-slots.show', $row->id) . '" 
                            class="text-blue-600 hover:text-blue-900" title="View">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>

                        <!-- Delete -->
                        <form action="' . route('admin.schedule-slots.destroy', $row->id) . '" method="POST" class="inline">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="button" 
                                onclick="confirmDelete(\'' . $row->customer->first_name . ' Schedule Slot\', this.form)"
                                class="text-red-600 hover:text-red-900" 
                                title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>

                    </div>
                ';
            })

            ->rawColumns(['language', 'status', 'actions'])

            ->make(true);
    }

    public function show(ScheduleSlot $scheduleSlot)
    {
        $scheduleSlot->load(['customer', 'service']);
        return view('admin.schedule-slots.show', compact('scheduleSlot'));
    }

    public function updateStatus(Request $request, ScheduleSlot $scheduleSlot)
    {
        $request->validate([
            'status' => 'required|in:1,2,3,4',
        ]);

        $scheduleSlot->update([
            'status' => $request->status
        ]);

        $stausText = [
            1 => 'Schedule',
            2 => 'Completed',
            3 => 'Cancelled',
            4 => 'Not Reachable',
        ];

        return back()->with('success', $stausText[$request->status] . ' status updated successfully.');
    }

    public function updateRemark(Request $request, ScheduleSlot $scheduleSlot)
    {
        $request->validate([
            'remark' => 'required|string'
        ]);

        $scheduleSlot->update([
            'remarks' => $request->remark
        ]);

        return back()->with('success', 'Remark updated successfully.');
    }

    public function destroy(ScheduleSlot $scheduleSlot)
    {
        $scheduleSlot->delete();

        return back()->with('success', 'Schedule slot deleted successfully.');
    }
}
