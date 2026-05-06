<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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
            'order_id',
            'inv_date',
            'inv_no',
            'net_amount',
            'cgst',
            'sgst',
            'igst',
            'total_amount'
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

            ->editColumn('inv_no', function ($row) {
                return $row->inv_no ?? 'N/A';
            })

            ->editColumn('inv_date', function ($row) {
                return $row->inv_date->format('d M Y');
            })

            ->addcolumn('total_amount', function ($row) {
                return '₹' . number_format($row->total_amount, 2);
            })

            ->addColumn('application_order_paymentid', function ($row) {
                return $row->order->payment_id ?? 'N/A';
            })

            ->filterColumn('customer_name', function ($query, $keyword) {
                $query->whereHas('customer', function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%")
                        ->orWhere('mobile_number', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('total_amount', function ($query, $keyword) {
                $query->where('total_amount', 'like', "%{$keyword}%");
            })

            ->addColumn('actions', function ($row) {
                return '
                    <div class="flex items-center gap-2">
                        <!-- View -->
                        <a href="' . route('admin.customers.show', $row->customer->id) . '#info" 
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

                        <!-- Download -->
                        <a href="' . route('admin.invoices.download', $row->id) . '" 
                            class="text-green-600 hover:text-green-900" target="_blank" title="Download">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </a>
                    </div>
                ';
            })

            ->rawColumns(['actions'])

            ->make(true);
    }

    public function download($invoice_id)
    {
        $invoice = DB::table('invoices')->where('id', $invoice_id)->first();

        if (!$invoice) {
            abort(404, 'Invoice not found');
        }

        $customer = DB::table('customers')->where('id', $invoice->customer_id)->first();

        $service = DB::table('services')
            ->where('id', $invoice->service_id)
            ->first();

        $order = DB::table('application_orders')
            ->where('id', $invoice->order_id)
            ->first();

        $paymentLog = null;

        if ($order) {
            $paymentLog = DB::table('razorpay_logs_entry')
                ->where('order_id', $order->id)
                ->latest()
                ->first();
        }

        $payment_amount = $paymentLog->order_amount
            ?? $order->amount
            ?? $invoice->total_amount
            ?? 0;

        $payment_mode = optional($paymentLog)->payment_mode ?? 'Online';

        $payment_id = $paymentLog->reference_id
            ?? $order->payment_id
            ?? 'N/A';

        $customer_state = strtoupper($customer->state ?? '');
        $is_gujarat = ($customer_state == 'GUJARAT');

        $gov_amount = $service->service_gov_amount ?? 0;
        $service_charges = $service->service_charges ?? 0;

        $gst_rate = 18;

        if ($is_gujarat) {
            $cgst = round($service_charges * ($gst_rate / 2) / 100, 2);
            $sgst = round($service_charges * ($gst_rate / 2) / 100, 2);
            $igst = 0;
        } else {
            $cgst = 0;
            $sgst = 0;
            $igst = round($service_charges * ($gst_rate / 100), 2);
        }

        $grand_total = $gov_amount + $service_charges + $cgst + $sgst + $igst;

        $pdf = Pdf::loadView('invoice.passport_invoice', [
            'customer'        => $customer,
            'service'         => $service,
            'invoice'         => $invoice,
            'payment_amount'  => $payment_amount,
            'payment_mode'    => $payment_mode,
            'payment_id'      => $payment_id,
            'gov_amount'      => $gov_amount,
            'service_charges' => $service_charges,
            'cgst'            => $cgst,
            'sgst'            => $sgst,
            'igst'            => $igst,
            'grand_total'     => $grand_total,
            'is_gujarat'      => $is_gujarat,
            'gst_rate'        => $gst_rate,
        ]);

        $fileName = "Invoice_" . ($invoice->inv_no ?? $invoice->id) . ".pdf";

        return $pdf->download($fileName);
    }

    public function gstIndex()
    {
        return view('admin.gst.index');
    }

    public function gstData(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = Invoice::with(['customer', 'order'])->select(
            'id',
            'customer_id',
            'service_id',
            'order_id',
            'inv_date',
            'inv_no',
            'net_amount',
            'cgst',
            'sgst',
            'igst',
            'total_amount'
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
                $firstName = $row->customer->first_name ?? '';
                $lastName  = $row->customer->last_name ?? '';
                $email     = $row->customer->email ?? '';

                $fullName = trim($firstName . ' ' . $lastName);

                return '
                    <div>
                        <div class="font-semibold text-gray-900">' . ($fullName ?: '-') . '</div>
                        <div class="text-xs text-gray-500">' . $email . '</div>
                    </div>
                ';
            })

            ->addColumn('customer_mobile', function ($row) {
                return $row->customer->mobile_number;
            })

            ->addColumn('customer_city', function ($row) {
                return $row->customer->city;
            })

            ->addColumn('customer_state', function ($row) {
                return $row->customer->state;
            })

            ->editColumn('inv_no', function ($row) {
                return $row->inv_no ?? 'N/A';
            })

            ->editColumn('inv_date', function ($row) {
                return $row->inv_date->format('d M Y');
            })

            ->addColumn('net_amount', function ($row) {
                return '₹' . number_format($row->net_amount, 2);
            })

            ->addColumn('cgst', function ($row) {
                return '₹' . number_format($row->cgst, 2);
            })

            ->addColumn('sgst', function ($row) {
                return '₹' . number_format($row->sgst, 2);
            })

            ->addColumn('igst', function ($row) {
                return '₹' . number_format($row->igst, 2);
            })

            ->addcolumn('total_amount', function ($row) {
                return '₹' . number_format($row->total_amount, 2);
            })

            ->addColumn('application_order_paymentid', function ($row) {
                return $row->order->payment_id ?? 'N/A';
            })

            ->filterColumn('customer_name', function ($query, $keyword) {
                $query->whereHas('customer', function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('mobile_number', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('customer_mobile', function ($query, $keyword) {
                $query->whereHas('customer', function ($q) use ($keyword) {
                    $q->where('mobile_number', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('customer_city', function ($query, $keyword) {
                $query->whereHas('customer', function ($q) use ($keyword) {
                    $q->where('city', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('customer_state', function ($query, $keyword) {
                $query->whereHas('customer', function ($q) use ($keyword) {
                    $q->where('state', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('net_amount', function ($query, $keyword) {
                $query->where('net_amount', 'like', "%{$keyword}%");
            })

            ->filterColumn('cgst', function ($query, $keyword) {
                $query->where('cgst', 'like', "%{$keyword}%");
            })

            ->filterColumn('sgst', function ($query, $keyword) {
                $query->where('sgst', 'like', "%{$keyword}%");
            })

            ->filterColumn('igst', function ($query, $keyword) {
                $query->where('igst', 'like', "%{$keyword}%");
            })

            ->filterColumn('total_amount', function ($query, $keyword) {
                $query->where('total_amount', 'like', "%{$keyword}%");
            })

            ->rawColumns(['customer_name'])

            ->make(true);
    }
}
