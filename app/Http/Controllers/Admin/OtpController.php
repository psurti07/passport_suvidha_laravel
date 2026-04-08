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
            ])

            ->whereBetween('sent_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);

        if ($request->filled('is_verified')) {
            if ($request->is_verified == '1') {
                $query->where('is_verified', 1);
            }

            if ($request->is_verified == '0') {
                $query->where('is_verified', 0);
            }
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('is_verified', function ($row) {
                return $row->is_verified ? 1 : 0;
            })

            ->editColumn('sent_at', function ($row) {
                return $row->sent_at->format('d/m/Y H:i:s');
            })

            ->make(true);
    }
}
