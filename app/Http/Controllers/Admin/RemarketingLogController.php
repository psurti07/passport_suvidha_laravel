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
                } else if ($row->type == 'interakt') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">Interakt</span>';
                } else if ($row->type == 'rcs') {
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
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
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
