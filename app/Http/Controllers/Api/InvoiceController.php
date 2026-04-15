<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;

class InvoiceController extends Controller
{
    public function generateInvoice($customer_id)
    {

        $customer = DB::table('customers')->where('id', $customer_id)->first();
        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $invoice = DB::table('invoices')
            ->where('customer_id', $customer->id)
            ->orderBy('id', 'desc')
            ->first();

        $service = null;

        if ($invoice) {
            $service = DB::table('services')
                ->where('id', $invoice->service_id)
                ->first();
        }

        $order = DB::table('application_orders')
            ->where('customer_id', $customer->id)
            ->orderBy('id', 'desc')
            ->first();

        $paymentLog = null;
        
        if ($order) {
            $paymentLog = DB::table('razorpay_logs_entry')
                ->where('order_id', $order->id)
                ->orderBy('id', 'desc')
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

        $pdf = PDF::loadView('invoice.passport_invoice', [
            'customer'       => $customer,
            'service'        => $service,
            'invoice'        => $invoice,
            'payment_amount' => $payment_amount,
            'payment_mode'   => $payment_mode,
            'payment_id'     => $payment_id,
        ]);
        
        $fileName = "Invoice_" . ($invoice->inv_no ?? time()) . ".pdf";

        return $pdf->stream($fileName);
    }
}