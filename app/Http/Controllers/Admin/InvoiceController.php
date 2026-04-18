<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Yajra\DataTables\Facades\DataTables;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('admin.invoices.index');
    }

    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = Invoice::with(['customer', 'order'])->select(
            'id',
            'customer_id',
            'service_id',
            'card_id',
            'inv_date',
            'inv_no',
            'net_amount',
            'cgst',
            'sgst',
            'igst',
            'total_amount',
            'created_at'
        )->latest('inv_date');
        
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('inv_date', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);
        }
        
        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('customer_name', function ($row) {
                return $row->customer->first_name . ' ' . $row->customer->last_name;
            })

            ->addColumn('customer_mobile', function ($row) {
                return $row->customer->mobile_number;
            })

            ->addColumn('inv_no', function ($row) {
                return $row->inv_no ?? 'N/A';
            })

            ->editColumn('inv_date', function ($row) {
                return $row->inv_date->format('d/m/Y H:i:s');
            })

            ->addcolumn('total_amount', function ($row) {
                return '₹' . number_format($row->total_amount, 2);
            })

            ->addColumn('application_order_paymentid', function ($row) {
                return $row->order->payment_id ?? 'N/A';
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y H:i:s');
            })

            ->addColumn('actions', function ($row) {
                return '
                    <a href="'.route('admin.customers.show', $row->customer->id).'#info" 
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

            ->rawColumns(['actions'])

            ->make(true);
    }
}
