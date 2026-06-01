<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmsLog;
use Yajra\DataTables\Facades\DataTables;

class RemarketingLogController extends Controller
{
    public function index()
    {
        return view('admin.remarketing-logs.index');
    }

    public function data(Request $request)
    {
        $query = SmsLog::select([
            'id',
            'type',
            'crontype',
            'cronname',
            'msgcount',
            'msgresponse',
            'created_at',
        ]);

        if ($request->filled('from_date') && $request->filled('to_date')) {

            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->editColumn('type', function ($row) {
                if ($row->type == 'sms') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-800">SMS</span>';
                } else if($row->type == 'aisensy') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">Aisensy</span>';
                } else if($row->type == 'interakt') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">Interakt</span>';
                } else if($row->type == 'rcs') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-red-100 text-red-800">RCS</span>';
                }
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y, h:i A');
            })

            ->addColumn('actions', function ($row) {
                return '
                    <div class="flex items-center gap-2">
                    
                        <!-- View -->
                        <a href="' . route('admin.remarketing-logs.show', $row->id) . '" 
                            class="text-blue-600 hover:text-blue-900" title="View">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>

                    </div>
                ';
            })

            ->rawColumns(['type', 'actions'])

            ->make(true);
    }

    public function show($id)
    {
        $smsLog = SmsLog::findOrFail($id);
        return view('admin.remarketing-logs.show', compact('smsLog'));
    }
}
