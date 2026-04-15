<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RazorpayLog;
use Yajra\DataTables\Facades\DataTables;

class RazorpayLogController extends Controller
{
    public function index()
    {
        return view('admin.razorpay-logs.index');
    }

    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = RazorpayLog::with('customer')->select([
            'id',
            'customer_id',
            'order_id',
            'order_amount',
            'order_note',
            'reference_id',
            'tx_status',
            'payment_mode',
            'created_at'
        ]);

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);
        }
        
        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('name', function ($row) {
                return $row->customer->first_name . ' ' . $row->customer->last_name;
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y H:i:s');
            })

            ->make(true);
    }
}
