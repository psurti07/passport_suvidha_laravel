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
        $request->validate([
            'service_code' => 'required',
            'mobile' => 'required'
        ]);

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        $service = Service::where('service_code', $request->service_code)->first();

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid service'
            ], 400);
        }

        $amount = $service->service_total_amount;
        $finalAmount = $amount;

        $testNumbers = explode(',', config('services.testnumbers.test_numbers', ''));

        if (in_array($request->mobile, $testNumbers)) {
            $finalAmount = 1;
        }

        $razorpayAmount = $finalAmount * 100;

        $order = $api->order->create([
            'receipt' => 'order_' . time(),
            'amount' => $razorpayAmount,
            'currency' => 'INR'
        ]);

        RazorpayLog::create([
            'customer_id' => auth()->id() ?? 0,
            'order_id' => null,
            'payment_id' => null,
            'order_amount' => $finalAmount,
            'order_note' => 'Passport Application',
            'reference_id' => $order['id'],
            'tx_status' => null,
            "service_type" => $request->service_code,
        ]);

        $customer = auth()->user();

        $full_name = $customer?->full_name;

        return response()->json([
            'id' => $order['id'],
            'amount' => $razorpayAmount,
            'name' => $full_name,
            'email' => $customer?->email,
            'mobile' => $customer?->mobile_number,
        ]);
    }
    public function verifyPayment(Request $request, BrevoMailService $brevoMailService)
    {

        // Log::info('VERIFY PAYMENT START 1', [
        //     'request' => $request->all()
        // ]);
        $api = new Api(config("services.razorpay.key"), config("services.razorpay.secret"));

        try {

            $request->validate([
                'razorpay_order_id' => 'required',
                'razorpay_payment_id' => 'required',
                'razorpay_signature' => 'required',
            ]);

            // Log::info('VALIDATION PASSED 2');

            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            // Log::info('VERIFYING SIGNATURE 3', $attributes);

            $api->utility->verifyPaymentSignature($attributes);

            // Log::info('SIGNATURE VERIFIED SUCCESSFULLY 4');

            $log = RazorpayLog::where('reference_id', $request->razorpay_order_id)->first();

            // Log::info('ORDER LOG FOUND 5', [
            //     'log_id' => $log?->id,
            //     'customer_id' => $log?->customer_id,
            //     'reference_id' => $log?->reference_id,
            // ]);

            if (!$log) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order log not found'
                ], 400);
            }

            $customer = Customer::find($log->customer_id);

            // Log::info('CUSTOMER FOUND 6', [
            //     'customer_id' => $customer?->id,
            //     'is_paid' => $customer?->is_paid,
            // ]);

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

            // Log::info('PAYMENT FETCHED FROM RAZORPAY 7', [
            //     'payment_id' => $payment->id,
            //     'status' => $payment->status,
            //     'method' => $payment->method,
            //     'amount' => $payment->amount,
            //     'captured' => $payment->captured ?? null,
            // ]);

            if ($payment->status !== 'captured') {

                // Log::warning('PAYMENT NOT CAPTURED 8', [
                //     'payment_id' => $payment->id,
                //     'status' => $payment->status
                // ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Payment not captured'
                ]);
            }

            $paymentMode = $payment->method;

            // Log::info('DB TRANSACTION STARTED 9');

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
                    'is_active' => 1
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

            // Log::info('APPLICATION ORDER CREATED 10', [
            //     'order_id' => $order->id
            // ]);

            $log->update([
                'tx_status' => 'success',
                'payment_mode' => $paymentMode,
                'payment_id' => $request->razorpay_payment_id,
                'order_id' => $order->id
            ]);

            // Log::info('RAZORPAY LOG UPDATED SUCCESS 11');

            $customer->update([
                'is_paid' => 1,
                'is_active' => 1,
                'payment_date' => now()
            ]);

            // Log::info('RAZORPAY LOG UPDATED SUCCESS 12');

            $service = $customer->service;

            // $govAmount = $service->service_gov_amount ?? 0;
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

            // Log::info('INVOICE CREATED 13', [
            //     'invoice_id' => $invoice->id
            // ]);

            // Log::info('DB COMMIT START 14');

            DB::commit();

            // Log::info('DB COMMIT SUCCESS 15');

            try {

                // Log::info('EMAIL PROCESS STARTED 16', [
                //     'customer_id' => $customer->id ?? null,
                //     'email' => $customer->email ?? null,
                // ]);

                if ($customer && !empty($customer->email)) {

                    // Log::info('Customer found for email sending 17', [
                    //     'customer_id' => $customer->id,
                    //     'email' => $customer->email,
                    // ]);

                    $invoice = Invoice::with(['customer', 'order', 'service'])
                        ->find($invoice->id);

                    // Log::info('Invoice loaded 18', [
                    //     'invoice_id' => $invoice?->id,
                    //     'invoice_no' => $invoice?->inv_no,
                    // ]);

                    $pdf = InvoiceController::getInvoicePdf($customer->id);

                    // Log::info('Invoice PDF generated successfully 19');

                    $attachments = [
                        [
                            'name' =>  'Passport-Suvidha-Invoice-' . time() . '.pdf',
                            'content' => base64_encode($pdf->output()),
                            'contentType' => 'application/pdf'
                        ]
                    ];

                    // Log::info('Attachment prepared 20', [
                    //     'file_name' => 'Passport-Suvidha-Invoice-' . time() . '.pdf',
                    // ]);

                    // Log::info('Email HTML rendered successfully 21');

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

            // Log::info('FACEBOOK EVENT START 24');

            $facebookService = new FacebookConversionService();
            $facebookService->send($customer);

            // Log::info('FACEBOOK EVENT SUCCESS 25');


            if (isset($customer)) {
                $this->tracking_success($customer);
            }

            if (!empty($customer->mobile_number)) {

                // Log::info('SMS PROCESS START 26');
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

                // Log::info('SMS PROCESS SUCCESS 27');

                // $message = $smsMessage['message'];

                // $smsService->sendSms($customer->mobile_number, $message);
            }

            // Log::info('VERIFY PAYMENT COMPLETED SUCCESSFULLY 28');

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

            // Log::info('PAYMENT STATUS DURING EXCEPTION 31', [
            //     'payment_id' => $payment->id ?? null,
            //     'status' => $payment->status ?? null,
            //     'method' => $payment->method ?? null
            // ]);

            $log = RazorpayLog::where('reference_id', $request->razorpay_order_id)->first();

            // if ($log) {
            //     Log::warning('MARKING PAYMENT FAILED');
            //     $log->update([
            //         'tx_status' => 'failed',
            //         'payment_id' => $request->razorpay_payment_id ?? null,
            //         'payment_mode' => $paymentMode
            //     ]);
            //     Log::warning('PAYMENT MARKED FAILED IN DATABASE');
            // }

            if ($log) {

                if (
                    isset($payment) &&
                    $payment->status === 'captured'
                ) {

                    Log::warning('PAYMENT CAPTURED IN RAZORPAY - NOT MARKING FAILED 32', [
                        'payment_id' => $payment->id
                    ]);
                } else {

                    // Log::warning('MARKING PAYMENT FAILED 33');

                    $log->update([
                        'tx_status' => 'failed',
                        'payment_id' => $request->razorpay_payment_id ?? null,
                        'payment_mode' => $paymentMode
                    ]);

                    // Log::warning('PAYMENT MARKED FAILED IN DATABASE 34');
                }
            }

            if (isset($customer)) {
                $this->tracking_failed($customer);
            }

            if (isset($customer) && !empty($customer->mobile_number)) {
                $smsService = new SmsService();

                // $url = "https://passportsuvidha.com/cardoffer";

                $smsMessage = $smsService->sendTemplateSms($customer->mobile_number, 'payment-failed-sms');

                if (!$smsMessage['success']) {
                    return response([
                        'success' => false,
                        'message' => "SMS template not found"
                    ]);
                }

                // $message = str_replace('{#var#}', $paymentMode ?? 'UPI', $smsMessage['message']);

                // $smsService->sendSms($customer->mobile_number, $message);
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
        // Log::warning('PAYMENT FAILED API CALLED 36', [
        //     'request' => $request->all()
        // ]);
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'nullable'
        ]);

        $log = RazorpayLog::where('reference_id', $request->razorpay_order_id)->first();
        // Log::info('PAYMENT FAILED LOG FOUND 37', [
        //     'log_id' => $log?->id,
        //     'tx_status' => $log?->tx_status
        // ]);


        if ($log && $log->tx_status !== 'success') {

            $paymentMode = null;

            if (!empty($request->razorpay_payment_id)) {
                try {
                    $api = new Api(config("services.razorpay.key"), config("services.razorpay.secret"));
                    $payment = $api->payment->fetch($request->razorpay_payment_id);
                    // Log::info('PAYMENT FAILED STATUS FROM RAZORPAY 38', [
                    //     'payment_id' => $payment->id ?? null,
                    //     'status' => $payment->status ?? null,
                    //     'method' => $payment->method ?? null,
                    // ]);
                    if ($payment->status == 'captured') {

                        // Log::warning('PAYMENT CAPTURED IN RAZORPAY, FAILED CALLBACK IGNORED 39');

                        return response()->json([
                            'success' => true
                        ]);
                    }
                    $paymentMode = $payment->method;
                } catch (\Exception $e) {
                    Log::error('PAYMENT FETCH ERROR: ' . $e->getMessage());
                }
            }
            // Log::warning('UPDATING FAILED STATUS 40');
            $log->update([
                'tx_status' => 'failed',
                'payment_id' => $request->razorpay_payment_id ?? null,
                'payment_mode' => $paymentMode
            ]);
            // Log::warning('FAILED STATUS UPDATED 41');

            $customer = Customer::find($log->customer_id);

            if (isset($customer)) {
                $this->tracking_failed($customer);
            }

            if ($customer && !empty($customer->mobile_number)) {

                $mobileNumber = $customer->mobile_number;

                $smsService = new SmsService();
                // $url = "https://passportsuvidha.com/cardoffer";
                $smsMessage = $smsService->sendTemplateSms($mobileNumber, 'payment-failed-sms');

                if (!$smsMessage['success']) {
                    return response([
                        'success' => false,
                        'message' => "SMS template not found"
                    ]);
                }

                // $message = str_replace('{#var_method#}', $paymentMode ?? '', $smsMessage['message']);
                // $smsService->sendSms($mobileNumber, $message);
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
        // Log::warning('PAYMENT FAILED METHOD FINISHED 42');

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

            // Log::info('Tracking Debug sucessfull', [
            //     'user_track' => $userTrack,
            //     'event_track' => $eventTrack,
            // ]);
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
