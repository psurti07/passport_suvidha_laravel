<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class OtpController extends Controller
{
    public function index()
    {
        return view('admin.otps.index');
    }

    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = Otp::select([
            'id',
            'mobile_number',
            'otp',
            'sent_at',
            'is_verified',
            'purpose'
        ])

        ->whereBetween('sent_at', [
            $from . ' 00:00:00',
            $to . ' 23:59:59'
        ]);

        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->is_verified);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->editColumn('sent_at', function ($row) {
                return $row->sent_at->format('d M Y, h:i A');
            })

            ->editColumn('is_verified', function ($row) {
                if ($row->is_verified == '1') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">Verified</span>';
                }  else {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">Pending</span>';
                }
            })

            ->rawColumns(['is_verified'])

            ->make(true);
    }
}
