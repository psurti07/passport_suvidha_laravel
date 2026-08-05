<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApplicationOrder;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\RazorpayLog;
use App\Models\Service;
use App\Services\BrevoMailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\SmsService;
use App\Services\FacebookConversionService;
use App\Services\ConversionTrackingService;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    protected ConversionTrackingService $trakingService;
    public function __construct(ConversionTrackingService $trakingService)
    {
        $this->trakingService = $trakingService;
    }

    public function createOrder(Request $request)
    {
        // $request->validate([
        //     // 'service_code' => 'required',
        //     'mobile' => 'required'
        // ]);

        $customer = auth()->user();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $service_id = $customer->service_id;

        if ($customer->is_paid) {
            return response()->json([
                'success' => false,
                'message' => 'Payment has already been completed.'
            ], 200);
        }

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        $service = Service::find($service_id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid service'
            ], 400);
        }

        $amount = $service->service_total_amount;
        $finalAmount = $amount;

        $testNumbers = explode(',', config('services.testnumbers.test_numbers', ''));

        if (in_array($customer->mobile_number, $testNumbers)) {
            $finalAmount = 1;
        }

        $razorpayAmount = $finalAmount * 100;

        $order = $api->order->create([
            'receipt' => 'order_' . time(),
            'amount' => $razorpayAmount,
            'currency' => 'INR'
        ]);

        RazorpayLog::create([
            'customer_id' => $customer->id,
            'order_id' => null,
            'payment_id' => null,
            'order_amount' => $finalAmount,
            'order_note' => 'Passport Application',
            'reference_id' => $order['id'],
            'tx_status' => null,
            "service_type" => $service->service_code,
        ]);

        return response()->json([
            'id' => $order['id'],
            'amount' => $razorpayAmount,
            'name' => $customer?->full_name,
            'email' => $customer?->email,
            'mobile' => $customer?->mobile_number,
        ]);
    }
    public function verifyPayment(Request $request, BrevoMailService $brevoMailService)
    {

        $api = new Api(config("services.razorpay.key"), config("services.razorpay.secret"));

        try {

            $request->validate([
                'razorpay_order_id' => 'required',
                'razorpay_payment_id' => 'required',
                'razorpay_signature' => 'required',
            ]);

            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            $log = RazorpayLog::where('reference_id', $request->razorpay_order_id)->first();

            if (!$log) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order log not found'
                ], 400);
            }

            $customer = Customer::find($log->customer_id);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 400);
            }

            if ($customer->is_paid == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment already completed'
                ], 400);
            }

            $payment = $api->payment->fetch($request->razorpay_payment_id);

            if ($payment->status !== 'captured') {

                return response()->json([
                    'success' => false,
                    'message' => 'Payment not captured'
                ]);
            }

            $paymentMode = $payment->method;

            DB::beginTransaction();

            $finalAmount = $log->order_amount;

            if ($finalAmount == 1) {

                $log->update([
                    'tx_status' => 'success',
                    'payment_mode' => $paymentMode,
                    'payment_id' => $request->razorpay_payment_id
                ]);

                $customer->update([
                    'is_paid' => 1,
                    'is_active' => 1,
                    'registration_step' => 5,
                    'payment_date' => now(),
                ]);

                DB::commit();

                if (isset($customer)) {
                    $this->tracking_success($customer);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Test payment successful (₹1)'
                ]);
            }

            $order = ApplicationOrder::updateOrCreate(
                ['customer_id' => $log->customer_id],
                [
                    'customer_id' => $log->customer_id,
                    'card_number' => generateCardNumber(),
                    'amount' => $log->order_amount,
                    'payment_id' => $request->razorpay_payment_id,
                ]
            );

            $log->update([
                'tx_status' => 'success',
                'payment_mode' => $paymentMode,
                'payment_id' => $request->razorpay_payment_id,
                'order_id' => $order->id
            ]);

            $customer->update([
                'is_paid' => 1,
                'is_active' => 1,
                'registration_step' => 5,
                'payment_date' => now(),
            ]);

            $service = $customer->service;

            $serviceCharges = $service->service_charges ?? 0;

            $netAmount = $serviceCharges;

            $cgst = $sgst = $igst = 0;

            if (strtoupper($customer->state) == 'GUJARAT') {
                $cgst = round($serviceCharges * 0.09, 4);
                $sgst = round($serviceCharges * 0.09, 4);
                $total = $netAmount + $cgst + $sgst;
            } else {
                $igst = round($serviceCharges * 0.18, 2);
                $total = $netAmount + $igst;
            }

            $invoice = Invoice::create([
                'customer_id' => $log->customer_id,
                'service_id' => $customer->service_id,
                'order_id' => $order->id,
                'inv_date' => now(),
                'net_amount' => $netAmount,
                'cgst' => $cgst,
                'sgst' => $sgst,
                'igst' => $igst,
                'total_amount' => $total,
            ]);

            $invoice->update([
                'inv_no' => 'INV_' . $invoice->id
            ]);

            DB::commit();

            try {

                if ($customer && !empty($customer->email)) {

                    $invoice = Invoice::with(['customer', 'order', 'service'])
                        ->find($invoice->id);

                    $pdf = InvoiceController::getInvoicePdf($customer->id);

                    $attachments = [
                        [
                            'name' =>  'Passport-Suvidha-Invoice-' . time() . '.pdf',
                            'content' => base64_encode($pdf->output()),
                            'contentType' => 'application/pdf'
                        ]
                    ];

                    /*
                    |------------------------------------------------
                    |1. WELCOME EMAIL (NO ATTACHMENT)
                    |------------------------------------------------
                    */

                    $welcomeHtml = view('emails.welcome', [
                        'customer' => $customer
                    ])->render();

                    $brevoMailService
                        ->sendBrevoHtmlMail(
                            $customer->email,
                            $customer->full_name,
                            'Welcome to Passport Suvidha',
                            $welcomeHtml
                        );

                    /*
                    |------------------------------------------------
                    | 2. INVOICE EMAIL (WITH ATTACHMENT)
                    |------------------------------------------------
                    */
                    $invoiceHtml  = view(
                        'emails.invoice',
                        [
                            'customer' => $customer,
                            'invoice' => $invoice,
                            'payment_id' => $request->razorpay_payment_id,
                            'payment_date' => now(),
                        ]
                    )->render();

                    $brevoMailService->sendBrevoHtmlMailWithAttachments(
                        $customer->email,
                        $customer->full_name,
                        'Payment Successful',
                        $invoiceHtml,
                        $attachments
                    );

                    Log::info('EMAIL SENT SUCCESSFULLY 22', [
                        'customer_id' => $customer->id,
                        'invoice_no' => $invoice->inv_no,
                        'email' => $customer->email,
                    ]);
                } else {

                    Log::warning('Email skipped because customer email is missing 23', [
                        'customer_id' => $customer->id ?? null
                    ]);
                }
            } catch (\Exception $e) {

                Log::error('EMAIL FAILED', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'customer_id' => $customer->id ?? null,
                    'invoice_id' => $invoice->id ?? null,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            $facebookService = new FacebookConversionService();
            $facebookService->send($customer);

            if (isset($customer)) {
                $this->tracking_success($customer);
            }

            if (!empty($customer->mobile_number)) {

                $smsService = new SmsService();
                $smsMessageSuccess = $smsService->sendTemplateSms($customer->mobile_number, 'application-submitted-sms');
                if (!$smsMessageSuccess['success']) {
                    return response([
                        'success' => false,
                        'message' => "SMS template not found"
                    ]);
                }

                $smsMessageAccount = $smsService->sendTemplateSms($customer->mobile_number, 'account-sms');
                if (!$smsMessageAccount['success']) {
                    return response([
                        'success' => false,
                        'message' => "SMS template not found"
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully'
            ]);
        } catch (\Exception $e) {

            Log::error('VERIFY PAYMENT EXCEPTION 29', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            $paymentMode = null;

            if (!empty($request->razorpay_payment_id)) {
                try {
                    $payment = $api->payment->fetch($request->razorpay_payment_id);
                    $paymentMode = $payment->method;
                } catch (\Exception $ex) {
                    Log::error('PAYMENT FETCH ERROR: 30' . $ex->getMessage());
                }
            }

            $log = RazorpayLog::where('reference_id', $request->razorpay_order_id)->first();

            if ($log) {

                if (
                    isset($payment) &&
                    $payment->status === 'captured'
                ) {

                    Log::warning('PAYMENT CAPTURED IN RAZORPAY - NOT MARKING FAILED 32', [
                        'payment_id' => $payment->id
                    ]);
                } else {

                    $log->update([
                        'tx_status' => 'failed',
                        'payment_id' => $request->razorpay_payment_id ?? null,
                        'payment_mode' => $paymentMode
                    ]);
                }
            }

            if (isset($customer)) {
                $this->tracking_failed($customer);
            }

            if (isset($customer) && !empty($customer->mobile_number)) {
                $smsService = new SmsService();

                $smsMessage = $smsService->sendTemplateSms($customer->mobile_number, 'payment-failed-sms');

                if (!$smsMessage['success']) {
                    return response([
                        'success' => false,
                        'message' => "SMS template not found"
                    ]);
                }
            }

            try {
                $failePayment = view('emails.payment_failed', [
                    'customer' => $customer
                ])->render();

                $brevoMailService
                    ->sendBrevoHtmlMail(
                        $customer->email,
                        $customer->full_name,
                        'Payment Failed',
                        $failePayment
                    );
            } catch (\Exception $e) {

                Log::error('EMAIL FAILED 35', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'customer_id' => $customer->id ?? null,
                    'invoice_id' => $invoice->id ?? null,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed'
            ], 400);
        }
    }

    public function paymentFailed(Request $request, BrevoMailService $brevoMailService)
    {

        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'nullable'
        ]);

        $log = RazorpayLog::where('reference_id', $request->razorpay_order_id)->first();

        if ($log && $log->tx_status !== 'success') {

            $paymentMode = null;

            if (!empty($request->razorpay_payment_id)) {
                try {
                    $api = new Api(config("services.razorpay.key"), config("services.razorpay.secret"));
                    $payment = $api->payment->fetch($request->razorpay_payment_id);
                    if ($payment->status == 'captured') {

                        return response()->json([
                            'success' => true
                        ]);
                    }
                    $paymentMode = $payment->method;
                } catch (\Exception $e) {
                    Log::error('PAYMENT FETCH ERROR: ' . $e->getMessage());
                }
            }

            $log->update([
                'tx_status' => 'failed',
                'payment_id' => $request->razorpay_payment_id ?? null,
                'payment_mode' => $paymentMode
            ]);

            $customer = Customer::find($log->customer_id);

            if (isset($customer)) {
                $this->tracking_failed($customer);
            }

            if ($customer && !empty($customer->mobile_number)) {

                $mobileNumber = $customer->mobile_number;

                $smsService = new SmsService();

                $smsMessage = $smsService->sendTemplateSms($mobileNumber, 'payment-failed-sms');

                if (!$smsMessage['success']) {
                    return response([
                        'success' => false,
                        'message' => "SMS template not found"
                    ]);
                }
            }

            try {
                $failePayment = view('emails.payment_failed', [
                    'customer' => $customer
                ])->render();

                $brevoMailService
                    ->sendBrevoHtmlMail(
                        $customer->email,
                        $customer->full_name,
                        'Payment Failed',
                        $failePayment
                    );
            } catch (\Exception $e) {

                Log::error('EMAIL FAILED', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'customer_id' => $customer->id ?? null,
                    'invoice_id' => $invoice->id ?? null,
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment marked as failed'
        ]);
    }

    protected function tracking_success(Customer $customer)
    {
        try {

            $userTrack = $this->trakingService->userTrack([

                "phoneNumber" => $customer->mobile_number,
                "countryCode" => "+91",
                "traits" => [
                    "name" => $customer->full_name
                ],
                "tags" => ["Payment Successful"]

            ]);

            $eventTrack = $this->trakingService->eventTrack(
                [
                    "phoneNumber" => $customer->mobile_number,
                    "countryCode" => "+91",
                    "event" => "Payment Successful"
                ]
            );
        } catch (\Exception $e) {
            Log::error('Interakt Tracking Failed', [
                'message' => $e->getMessage()
            ]);
        }
    }

    protected function tracking_failed(Customer $customer)
    {
        try {

            $eventTrack = $this->trakingService->eventTrack(
                [
                    "phoneNumber" => $customer->mobile_number,
                    "countryCode" => "+91",
                    "event" => "Payment Failed"
                ]
            );

            Log::info('Tracking Debug failed', [
                'event_track' => $eventTrack,
            ]);
        } catch (\Exception $e) {
            Log::error('Interakt Tracking Failed', [
                'message' => $e->getMessage()
            ]);
        }
    }
}
