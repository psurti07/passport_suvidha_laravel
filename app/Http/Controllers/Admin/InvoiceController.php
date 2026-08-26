<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

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
        )->latest('created_at');

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('inv_date', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('customer_name', function ($row) {
                return Str::title(strtolower($row->customer->full_name));
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
                    $q->where('full_name', 'like', "%{$keyword}%")
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

                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />

                                </svg>
                            </a>


                            <!-- Download -->
                            <a href="' . route('admin.invoices.download', $row->id) . '"
                                class="text-green-600 hover:text-green-900"
                                target="_blank"
                                title="Download">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />

                                </svg>
                            </a>


                             <!-- Refund -->
                            <button type="button"
                                onclick="openRefundModal(
                                    \'' . $row->id . '\',
                                    \'' . ($row->order->payment_id ?? '') . '\',
                                    \'' . $row->total_amount . '\'
                                )"
                                class="text-purple-600 hover:text-purple-800"
                                title="Refund">
                                
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 14l-4-4 4-4" />

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 10h9a5 5 0 015 5v1" />

                                </svg>

                            </button>


                            <!-- Delete -->
                            <form action="' . route('admin.invoices.destroy', $row->id) . '"
                                method="POST"
                                class="inline">

                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '

                                <button type="button"
                                    onclick="confirmDelete(\'' . addslashes($row->customer->full_name) . ' Invoice\', this.form)"
                                    class="text-red-600 hover:text-red-900"
                                    title="Delete">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">

                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />

                                    </svg>
                                </button>
                            </form>

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
            $paymentLog = DB::table('razorpay_logs')
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

        // $gov_amount = $service->service_gov_amount ?? 0;
        $service_charges = $service->service_charges ?? 0;

        $gst_rate = 18;

        // if ($is_gujarat) {
        //     $cgst = round($service_charges * ($gst_rate / 2) / 100, 2);
        //     $sgst = round($service_charges * ($gst_rate / 2) / 100, 2);
        //     $igst = 0;
        // } else {
        //     $cgst = 0;
        //     $sgst = 0;
        //     $igst = round($service_charges * ($gst_rate / 100), 2);
        // }

        // $grand_total = $gov_amount + $service_charges + $cgst + $sgst + $igst;

        $pdf = Pdf::loadView('invoice.passport_invoice', [
            'customer'        => $customer,
            'service'         => $service,
            'invoice'         => $invoice,
            'payment_amount'  => $payment_amount,
            'payment_mode'    => $payment_mode,
            'payment_id'      => $payment_id,
            'net_amount'      => $invoice->net_amount,
            'service_charges' => $service_charges,
            'cgst'            => $invoice->cgst,
            'sgst'            => $invoice->sgst,
            'igst'            => $invoice->igst,
            'grand_total'     => $invoice->total_amount,
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
                $fullName = $row->customer->full_name ?? '';
                $email     = $row->customer->email ?? '';

                return '
                    <div>
                        <div class="font-semibold text-gray-900">' . (Str::title(strtolower($fullName)) ?: '-') . '</div>
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
                    $q->where('full_name', 'like', "%{$keyword}%")
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

    public function destroy(Invoice $invoice)
    {
        DB::transaction(function () use ($invoice) {

            $invoice->logs()->delete();

            $invoice->delete();
        });

        return back()->with('success', 'Invoice deleted successfully.');
    }
}
