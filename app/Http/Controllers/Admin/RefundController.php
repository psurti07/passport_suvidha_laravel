<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Illuminate\Support\Str;
use App\Models\ApplicationProgress;
use App\Models\Customer;
use App\Models\RazorpayLog;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

use Throwable;


class RefundController extends Controller
{

    public function index()
    {
        return view('admin.refund.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_id' => 'required|string|max:256',
            'remark' => 'required|string|max:255',
        ]);

        try {
            $invoice = Invoice::with([
                'order',
                'customer',
            ])->findOrFail($request->invoice_id);

            if (!$invoice) {
                return back()->with('error', 'Order not found for this invoice');
            }

            $paymentId = $invoice->order->payment_id;
            if (!$paymentId) {
                return back()->with('error', 'Payment ID not found for this order.');
            }

            if ($paymentId !== $request->payment_id) {
                return back()->with('error', 'Invalid payment ID.');
            }

            $refundAmount = (float) $invoice->total_amount;


            if ($refundAmount <= 0) {
                return back()->with('error', 'Invalid refund amount.');
            }

            $existingRefund = Refund::where(
                'invoice_id',
                $invoice->id
            )
                ->whereIn('status', [
                    'pending',
                    'processed'
                ])
                ->first();


            if ($existingRefund) {
                return back()->with('error', 'Refund already exists for this invoice.');
            }

            $refundNo =
                'REF-' .
                now()->format('YmdHis') .
                '-' .
                strtoupper(Str::random(6));

            $isCashPayment = str_starts_with(
                strtolower($paymentId),
                'cash_'
            );

            if ($isCashPayment) {
                $refund = Refund::create([
                    'customer_id' => $invoice->customer_id,
                    'order_id' => $invoice->order_id,
                    'invoice_id' => $invoice->id,
                    'payment_id' => $paymentId,
                    'refund_no' => $refundNo,

                    'amount' => $refundAmount,

                    // Cash refund is considered completed immediately
                    'status' => 'processed',

                    'remark' => $request->remark,

                    'refunded_at' => now(),
                ]);

                $refundStatus = 'processed';
            } else {

                $api = new Api(
                    config('services.razorpay.key'),
                    config('services.razorpay.secret')
                );

                $payment = $api->payment->fetch($paymentId);

                if ($payment->status !== 'captured') {
                    return back()->with('error', 'Payment is not captured.');
                }

                $paidAmount = ((float) $payment->amount) / 100;

                $alreadyRefunded = Refund::where(
                    'payment_id',
                    $paymentId
                )
                    ->whereIn('status', [
                        'pending',
                        'processed'
                    ])
                    ->sum('amount');

                $remainingAmount =
                    $paidAmount - $alreadyRefunded;


                if ($refundAmount > $remainingAmount) {
                    return back()->with('error', 'Refund amount exceeds refundable amount.');
                }

                $refundNo =
                    'REF-' .
                    now()->format('YmdHis') .
                    '-' .
                    strtoupper(Str::random(6));


                $refund = Refund::create([

                    'customer_id' => $invoice->customer_id,

                    'order_id' => $invoice->order_id,

                    'invoice_id' => $invoice->id,

                    'payment_id' => $paymentId,

                    'refund_no' => $refundNo,

                    'amount' => $refundAmount,

                    'status' => 'pending',

                    'remark' => $request->remark,

                ]);

                $razorpayRefund = $api
                    ->payment
                    ->fetch($paymentId)
                    ->refund([

                        'amount' => (int) round(
                            $refundAmount * 100
                        ),

                        'speed' => 'normal',

                        'receipt' => $refundNo,

                        'notes' => [

                            'refund_no' => $refundNo,

                            'invoice_id' =>
                            (string) $invoice->id,

                            'order_id' =>
                            (string) $invoice->order_id,

                        ]

                    ]);

                $refundStatus = $razorpayRefund->status ?? 'pending';

                $refund->update([

                    'refund_id' => $razorpayRefund->id ?? null,

                    'status' => $refundStatus,

                    'refunded_at' => ($razorpayRefund->status ?? null) === 'processed' ? now() : null,

                ]);
            }

            if ($refundStatus == 'processed') {
                DB::transaction(function () use ($invoice, $paymentId) {

                    ApplicationProgress::create([
                        'customer_id' => $invoice->customer_id,
                        'status_id' => 11,
                        'status_date' => now(),
                        'remark' => 'Application cancelled due to refund.',
                        'remarked_by' => auth()->id(),
                    ]);

                    $invoice->delete();

                    Customer::where('id', $invoice->customer_id)->update([
                        'registration_step' => 10,
                    ]);

                    RazorpayLog::where('payment_id', $paymentId)->update([
                        'tx_status' => 'refund',
                    ]);
                });
            }

            return back()->with('success', 'Refund initiated successfully.');
        } catch (Throwable $e) {

            Log::error('Refund failed', [
                'invoice_id' => $request->invoice_id,
                'payment_id' => $request->payment_id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Refund failed.');
        }
    }
    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = Refund::with([
            'customer',
            'order',
            'invoice'
        ])->select(
            'id',
            'customer_id',
            'payment_id',
            'refund_no',
            'refund_id',
            'amount',
            'status',
            'remark',
            'refunded_at',
            'created_at'
        )->latest('refunded_at');

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('refunded_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('customer_name', function ($row) {
                return Str::title(strtolower($row->customer->full_name))  ?? '-';
            })

            ->addColumn('customer_mobile', function ($row) {
                return $row->customer->mobile_number ?? '-';
            })

            ->addColumn('payment_id', function ($row) {
                return $row->payment_id ?? '-';
            })

            ->addColumn('refund_no', function ($row) {
                return $row->refund_no ?? '-';
            })

            ->addColumn('refund_id', function ($row) {
                return $row->refund_id ?? '-';
            })

            ->addColumn('amount', function ($row) {
                return '₹' . number_format($row->amount, 2);
            })

            ->addColumn('status', function ($row) {

                $status = strtolower($row->status);

                if ($status === 'processed') {
                    return '<span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded">Processed</span>';
                }

                if ($status === 'pending') {
                    return '<span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded">
                            Pending
                        </span>';
                }

                if ($status === 'failed') {
                    return '<span class="px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded">
                            Failed
                        </span>';
                }

                return '<span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                        ' . ucfirst($status) . '
                    </span>';
            })

            ->addColumn('refunded_at', function ($row) {
                return $row->refunded_at
                    ? $row->refunded_at->format('d M Y H:i')
                    : '-';
            })

            ->addColumn('remark', function ($row) {
                return $row->remark ?? '-';
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

            ->filterColumn('payment_id', function ($query, $keyword) {
                $query->where('payment_id', 'like', "%{$keyword}%");
            })

            ->filterColumn('refund_no', function ($query, $keyword) {
                $query->where('refund_no', 'like', "%{$keyword}%");
            })

            ->filterColumn('refund_id', function ($query, $keyword) {
                $query->where('refund_id', 'like', "%{$keyword}%");
            })

            ->filterColumn('amount', function ($query, $keyword) {
                $query->where('amount', 'like', "%{$keyword}%");
            })

            ->filterColumn('status', function ($query, $keyword) {
                $query->where('status', 'like', "%{$keyword}%");
            })

            ->rawColumns(['status'])

            ->make(true);
    }
}
