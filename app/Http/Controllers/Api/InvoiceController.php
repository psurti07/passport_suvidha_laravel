<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApplicationOrder;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\RazorpayLog;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{

    private function getInvoiceData($customer_id)
    {
        $customer = Customer::findOrFail($customer_id);

        $invoice = Invoice::where('customer_id', $customer->id)
            ->latest()
            ->first();

        $service = $invoice
            ? Service::find($invoice->service_id)
            : null;

        $order = ApplicationOrder::where('customer_id', $customer->id)
            ->latest()
            ->first();

        $paymentLog = $order
            ? RazorpayLog::where('order_id', $order->id)->latest()->first()
            : null;

        return [
            'customer'        => $customer,
            'service'         => $service,
            'invoice'         => $invoice,
            'payment_amount'  => $invoice->net_amount ?? 0,
            'payment_mode'    => optional($paymentLog)->payment_mode ?? 'Online',
            'payment_id'      => optional($paymentLog)->reference_id
                ?? optional($order)->payment_id
                ?? 'N/A',
            'net_amount'      => $invoice->net_amount ?? 0,
            'service_charges' => $service->service_charges ?? 0,
            'cgst'            => $invoice->cgst ?? 0,
            'sgst'            => $invoice->sgst ?? 0,
            'igst'            => $invoice->igst ?? 0,
            'grand_total'     => $invoice->total_amount ?? 0,
            'is_gujarat'      => strtoupper($customer->state) === 'GUJARAT',
            'gst_rate'        => 18,
        ];
    }

    public function generateInvoice($customer_id)
    {
        $data = $this->getInvoiceData($customer_id);

        $pdf = Pdf::loadView('invoice.passport_invoice', $data);

        $fileName = "Invoice_" .
            ($data['invoice']->inv_no ?? time()) .
            ".pdf";

        return $pdf->stream($fileName);
    }

    public static function getInvoicePdf($customer_id)
    {
        $controller = new self();

        $data = $controller->getInvoiceData($customer_id);

        return Pdf::loadView('invoice.passport_invoice', $data);
    }
}
