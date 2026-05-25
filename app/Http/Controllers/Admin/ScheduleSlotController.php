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

            ->whereBetween('date', [
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
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-orange-100 text-orange-800">Gujarati</span>';
                } else {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-800">Unknown</span>';
                }
            })

            ->editColumn('status', function ($row) {

                $statuses = [
                    1 => 'Schedule',
                    2 => 'Completed',
                    3 => 'Cancelled',
                    4 => 'Not Reachable',
                ];

                $html = '<select 
                            class="change-status border rounded-lg px-2 py-1 text-sm"
                            data-id="' . $row->id . '">';

                foreach ($statuses as $key => $value) {

                    $selected = $row->status == $key ? 'selected' : '';

                    $html .= '<option value="' . $key . '" ' . $selected . '>
                                ' . $value . '
                            </option>';
                }

                $html .= '</select>';

                return $html;
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

                        <!-- Delete -->
                        <form action="' . route('admin.schedule-slots.destroy', $row->id) . '" method="POST" class="inline">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="button" 
                                onclick="confirmDelete(\'' . $row->customer->first_name . ' Schedule Slot\', this.form)"
                                class="text-red-600 hover:text-red-900" 
                                title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>

                    </div>
                ';
            })

            ->rawColumns(['language', 'status', 'actions'])

            ->make(true);
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

        return response()->json([
            'success' => true,
            'message' => $stausText[$request->status] . ' status updated successfully.'
        ]);
    }

    public function destroy(ScheduleSlot $scheduleSlot)
    {
        $scheduleSlot->delete();

        return back()->with('success', 'Schedule slot deleted successfully.');
    }
}
