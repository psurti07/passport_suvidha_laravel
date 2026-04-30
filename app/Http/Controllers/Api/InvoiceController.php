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

        $customer_state = strtoupper($customer->state);
        $is_gujarat = ($customer_state == 'GUJARAT');

        $gov_amount = $service->service_gov_amount ?? 0;
        $service_charges = $service->service_charges ?? 0;

        $gst_rate = 18;

        if($is_gujarat) {
            $cgst = round($service_charges * ($gst_rate / 2) / 100, 2);
            $sgst = round($service_charges * ($gst_rate / 2) / 100, 2);
            $igst = 0;
        } else {
            $cgst = 0;
            $sgst = 0;
            $igst = round($service_charges * ($gst_rate / 100), 2);
        } 
        
        $grand_total = $gov_amount + $service_charges + $cgst + $sgst + $igst;

        $pdf = PDF::loadView('invoice.passport_invoice', [
            'customer'       => $customer,
            'service'        => $service,
            'invoice'        => $invoice,
            'payment_amount' => $payment_amount,
            'payment_mode'   => $payment_mode,
            'payment_id'     => $payment_id,
            'gov_amount'      => $gov_amount,
            'service_charges' => $service_charges,
            'cgst'           => $cgst,
            'sgst'           => $sgst,  
            'igst'           => $igst,
            'grand_total'    => $grand_total,
            'is_gujarat'     => $is_gujarat,
            'gst_rate'       => $gst_rate,
        ]);
        
        $fileName = "Invoice_" . ($invoice->inv_no ?? time()) . ".pdf";

        return $pdf->stream($fileName);
    }
}