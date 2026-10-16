<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApplicationOrder;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\OfferOrder;
use App\Models\CashfreeLog;
use App\Models\Customer;
use App\Models\PayuLog;
use App\Models\Service;
use App\Models\PhonepeLog;
use App\Services\BrevoMailService;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OfferOrderController extends Controller
{
    public function createPayment(Request $request)
    {
        $request->validate([
            'fullName' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            'offer_type' => 'required',
            'service_code' => 'required',
        ]);

        // $customer = Customer::where('mobile_number', $request->mobile)->first();
        // if ($customer && $customer->is_paid === true) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "You are already registered customer"
        //     ], 200);
        // }

        $service = Service::where('service_code', $request->service_code)->first();
        if (!$service) {
            return response()->json([
                "success" => false,
                "message" => "Invalid service code"
            ], 200);
        }
        $amount = $service->service_total_amount;

        $gateway = $this->getGatewayByOffer($request->offer_type);

        $offer_map = [
            'card_offer' => 1,
            'star_offer' => 2,
            'bumper_offer' => 3,
        ];

        $offer_type = $offer_map[$request->offer_type] ?? null;
        if (!$offer_type) {
            return response()->json([
                "success" => false,
                "message" => "Invalid offer type",
            ], 422);
        }

        // $existingOrder = OfferOrder::where('mobile', $request->mobile)
        //     ->where('offer_type', $offer_type)
        //     ->whereNotNull('payment_id') // means already paid
        //     ->first();

        // if ($existingOrder) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "You have already purchased this offer"
        //     ], 200);
        // }

        $finalAmount = $amount;

        $testNumbers = array_map('trim', explode(',', config('services.testnumbers.test_numbers', '')));

        if (in_array($request->mobile, $testNumbers)) {
            $finalAmount = 1;
        }

        $order = OfferOrder::create([
            'full_name'   => $request->fullName,
            'mobile'      => $request->mobile,
            'email'       => $request->email,
            'offer_type'  => $offer_type,
            'amount'      => $finalAmount,
            'service_code' => $request->service_code,
        ]);

        return $this->processPayment($gateway, $order, $request);
    }

    private function getGatewayByOffer($offerType)
    {
        return match ($offerType) {
            'card_offer' => 'cashfree',
            'star_offer' => 'phonepe',
            'bumper_offer' => 'payu',
            default => 'cashfree',
        };
    }

    private function processPayment($gateway, $order, $request)
    {
        switch ($gateway) {
            case 'cashfree':
                return $this->processCashfree($order, $request);

            case 'phonepe':
                return $this->processPhonepe($order, $request);

            case 'payu':
                return $this->processPayu($order, $request);

            default:
                return response()->json(['success' => false]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CASHFREE
    |--------------------------------------------------------------------------
    */
    private function processCashfree($order, $request)
    {
        $baseUrl = $this->getCashfreeBaseUrl();

        $orderId = "order_" . $order->id . "_" . Str::random(6);

        $response = Http::withHeaders([
            'x-client-id' => config('services.cashfree.key'),
            'x-client-secret' => config('services.cashfree.secret'),
            'x-api-version' => '2022-09-01',
        ])->post($baseUrl . '/orders', [
            "order_id" => $orderId,
            "order_amount" => $order->amount,
            "order_currency" => "INR",
            "customer_details" => [
                "customer_id" => (string)$order->id,
                "customer_name" => $request->fullName,
                "customer_email" => $request->email,
                "customer_phone" => $request->mobile,
            ],
            "order_meta" => [
                "notify_url" => $this->getWebhookUrl() . '/api/cashfree/webhook',
            ]

        ]);
        if (!$response->successful()) {
            return response()->json([
                "success" => false,
                "message" => 'payment gateway failed'
            ], 200);
        }

        CashfreeLog::create([
            // 'customer_id'  => $order->id,
            'offer_type' => $order->offer_type,
            'order_id'     => $order->id,
            'order_amount' => $order->amount,
            'order_note'   => 'Card Offer Payment',
            'reference_id' => $orderId,
            'tx_status'    => 'pending',
            'payment_mode' => null,
            'service_type' => $request->service_code ?? null,
        ]);

        return response()->json([
            "success" => true,
            "type" => "cashfree",
            "payment_session_id" => $response['payment_session_id'],
            "order_id" => $orderId
        ]);
    }

    public function cashfreeWebhook(Request $request, BrevoMailService $brevoMailService)
    {
        Log::info('WEBHOOK HIT');
        try {

            // Log::info('CASHFREE WEBHOOK', $request->all());

            $data = $request->all();

            $orderId = $data['data']['order']['order_id']
                ?? $data['order']['order_id']
                ?? null;

            if (!$orderId) {
                return response()->json([
                    'status' => 'invalid'
                ], 400);
            }

            $paymentStatus = strtoupper(
                $data['data']['payment']['payment_status']
                    ?? $data['payment']['payment_status']
                    ?? 'PENDING'
            );

            $cfPaymentId = $data['data']['payment']['cf_payment_id']
                ?? $data['payment']['cf_payment_id']
                ?? null;

            $paymentMode = $data['data']['payment']['payment_group']
                ?? $data['payment']['payment_group']
                ?? 'unknown';

            $log = CashfreeLog::where(
                'reference_id',
                $orderId
            )->first();

            if (!$log) {

                // Log::error('Cashfree log not found', [
                //     'order_id' => $orderId
                // ]);

                return response()->json([
                    'status' => 'not_found'
                ], 404);
            }

            // prevent duplicate success processing
            if (
                $log->tx_status === 'success'
                && $paymentStatus === 'SUCCESS'
            ) {
                return response()->json([
                    'status' => 'already_processed'
                ]);
            }

            $order = OfferOrder::find($log->order_id);
            if ($paymentStatus === 'SUCCESS') {

                $order = OfferOrder::find($log->order_id);

                if ($order && !$order->payment_id) {
                    $order->update([
                        'payment_id' => $cfPaymentId,
                        'card_number' => generateCardNumber(),
                    ]);
                }
                // Log::info('PAYMENT SUCCESS WEBHOOK', [
                //     'order_id' => $orderId,
                //     'payment_id' => $cfPaymentId,
                // ]);

                $log->update([
                    'payment_id' => $cfPaymentId,
                    'tx_status' => 'success',
                    'payment_mode' => $paymentMode
                ]);

                return response()->json([
                    'status' => 'success'
                ]);
            }

            if (
                in_array(
                    $paymentStatus,
                    ['FAILED', 'NOT_ATTEMPTED', 'USER_DROPPED', 'CANCELLED']
                )
            ) {
                // Log::info('PAYMENT FAILED WEBHOOK', [
                //     'order_id' => $orderId,
                //     'status' => $paymentStatus,
                // ]);

                $log->update([
                    'tx_status' => 'failed'
                ]);

                try {
                    $failePayment = view('emails.payment_failed', [
                        'customer' => $order
                    ])->render();

                    $brevoMailService
                        ->sendBrevoHtmlMail(
                            $order->email,
                            $order->full_name,
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

                return response()->json([
                    'status' => 'failed'
                ]);
            }

            return response()->json([
                'status' => 'pending'
            ]);
        } catch (\Exception $e) {

            // Log::error('CASHFREE WEBHOOK ERROR', [
            //     'message' => $e->getMessage(),
            //     'line' => $e->getLine(),
            // ]);

            return response()->json([
                'status' => 'error'
            ], 500);
        }
    }

    private function getWebhookUrl()
    {
        if (config('services.app.env') === 'local') {
            return config('services.app.ngrok_url');
        }

        return config('services.app.url');
    }

    public function checkPaymentStatus(Request $request, BrevoMailService $brevoMailService)
    {
        try {

            // Log::info('CHECK PAYMENT STATUS', $request->all());

            $request->validate([
                'order_id' => 'required'
            ]);

            $log = CashfreeLog::where(
                'reference_id',
                $request->order_id
            )->first();

            if (!$log) {

                return response()->json([
                    'status' => 'not_found'
                ], 404);
            }

            if ($log->tx_status === 'success') {

                return response()->json([
                    'status' => 'success'
                ]);
            }

            $baseUrl = $this->getCashfreeBaseUrl();

            $response = Http::withHeaders([
                'x-client-id'     => config('services.cashfree.key'),
                'x-client-secret' => config('services.cashfree.secret'),
                'x-api-version'   => '2022-09-01',
            ])->get(
                $baseUrl . "/orders/{$request->order_id}/payments"
            );

            // Log::info('CASHFREE STATUS RESPONSE', [
            //     'response' => $response->json()
            // ]);

            if (!$response->successful()) {

                return response()->json([
                    'status' => 'pending'
                ]);
            }

            $payments = $response->json();

            if (empty($payments)) {

                return response()->json([
                    'status' => 'pending'
                ]);
            }

            $successPayment = collect($payments)->firstWhere(
                'payment_status',
                'SUCCESS'
            );

            $order = OfferOrder::find($log->order_id);

            if ($successPayment) {

                $cfPaymentId = $successPayment['cf_payment_id'] ?? null;

                $order = OfferOrder::find($log->order_id);

                if ($order && !$order->payment_id) {

                    $order->update([
                        'payment_id' => $cfPaymentId,
                        'card_number' => generateCardNumber(),
                    ]);
                }

                $log->update([
                    'payment_id' => $cfPaymentId,
                    'tx_status'  => 'success',
                ]);


                try {
                    Log::info('CASHFREE SUCCESS EMAIL START');

                    Log::info('Order Email', [
                        'email' => $order->email ?? null,
                        'name' => $order->full_name ?? null,
                    ]);

                    $successPayment = view('emails.payment_success', [
                        'customer' => $order
                    ])->render();

                    Log::info('EMAIL VIEW RENDERED');

                    $brevoMailService->sendBrevoHtmlMail(
                        $order->email,
                        $order->full_name,
                        'Payment Success',
                        $successPayment
                    );

                    Log::info('EMAIL SENT SUCCESSFULLY');
                } catch (\Exception $e) {

                    Log::error('EMAIL FAILED', [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'order_id' => $order->id ?? null,
                        'email' => $order->email ?? null,
                    ]);
                }

                return response()->json([
                    'status' => 'success'
                ]);
            }

            $latestPayment = collect($payments)
                ->sortByDesc('cf_payment_id')
                ->first();

            $status = strtoupper(
                $latestPayment['payment_status'] ?? 'PENDING'
            );


            if (
                in_array(
                    $status,
                    ['FAILED', 'CANCELLED', 'USER_DROPPED', 'NOT_ATTEMPTED']
                )
            ) {

                $log->update([
                    'tx_status' => 'failed'
                ]);

                try {
                    $failePayment = view('emails.payment_failed', [
                        'customer' => $order
                    ])->render();

                    $brevoMailService
                        ->sendBrevoHtmlMail(
                            $order->email,
                            $order->full_name,
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

                return response()->json([
                    'status' => 'failed'
                ]);
            }

            return response()->json([
                'status' => 'pending'
            ]);
        } catch (\Exception $e) {

            // Log::error('CHECK PAYMENT STATUS ERROR', [
            //     'message' => $e->getMessage(),
            //     'line'    => $e->getLine(),
            // ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    private function getCashfreeBaseUrl()
    {
        return config("services.cashfree.mode") === 'production'
            ? 'https://api.cashfree.com/pg'
            : 'https://sandbox.cashfree.com/pg';
    }

    private function getPayUBaseUrl()
    {
        return config('services.payu.mode') === 'production'
            ? 'https://secure.payu.in/_payment'
            : 'https://test.payu.in/_payment';
    }

    private function isProduction()
    {
        return config('app.env') === 'production';
    }

    private function getPhonePeTokenUrl()
    {
        return $this->isProduction()
            ? 'https://api.phonepe.com/apis/identity-manager/v1/oauth/token'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox/v1/oauth/token';
    }

    private function getPhonePePayUrl()
    {
        return $this->isProduction()
            ? 'https://api.phonepe.com/apis/pg/checkout/v2/pay'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox/checkout/v2/pay';
    }

    private function getPhonePeOrderStatusUrl($merchantOrderId)
    {
        return $this->isProduction()
            ? "https://api.phonepe.com/apis/checkout/v2/order/{$merchantOrderId}/status"
            : "https://api-preprod.phonepe.com/apis/pg-sandbox/checkout/v2/order/{$merchantOrderId}/status";
    }

    private function getPhonepeAccessToken()
    {
        try {

            $response = Http::asForm()->post(
                $this->getPhonePeTokenUrl(),
                [
                    'client_id'      => config('services.phonepe.id'),
                    'client_secret'  => config('services.phonepe.secret'),
                    'client_version' => config('services.phonepe.version'),
                    'grant_type'     => 'client_credentials',
                ]
            );

            // Log::info('PHONEPE TOKEN RESPONSE', [
            //     'status' => $response->status(),
            //     'body'   => $response->body(),
            // ]);


            if (!$response->successful()) {

                // Log::error('PHONEPE TOKEN FAILED', [
                //     'status' => $response->status(),
                //     'body'   => $response->body(),
                //     'headers' => $response->headers(),
                // ]);

                return null;
            }

            return $response->json('access_token');
        } catch (\Exception $e) {

            // Log::error('PHONEPE TOKEN ERROR', [
            //     'message' => $e->getMessage()
            // ]);

            return null;
        }
    }

    private function processPhonepe($order, $request)
    {
        try {

            $accessToken = $this->getPhonepeAccessToken();


            if (!$accessToken) {

                return response()->json([
                    'success' => false,
                    'message' => 'Unable to generate PhonePe token'
                ], 500);
            }

            // $merchantOrderId =
            //     'order_' . strtoupper(Str::random(8));
            $merchantOrderId = generateReferenceId();

            $finalAmount = $order->amount;

            $testNumbers = array_map('trim', explode(',', config('services.testnumbers.test_numbers', '')));

            if (in_array($request->mobile, $testNumbers)) {
                $finalAmount = 1;
            }

            $payload = [
                "merchantOrderId" => $merchantOrderId,
                "amount" => $finalAmount * 100,
                "expireAfter" => 1200,

                "metaInfo" => [
                    "udf1" => (string) $order->id,
                    "udf2" => (string) $request->mobile,
                ],

                "paymentFlow" => [
                    "type" => "PG_CHECKOUT",
                    "merchantUrls" => [
                        "redirectUrl" => config('services.app.url') . 'api/phonepe/verify?order_id=' . $merchantOrderId
                    ]
                ]
            ];

            Log::info('PHONEPE CREATE REQUEST', $payload);

            $response = Http::withHeaders([
                'Authorization' => 'O-Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->post(
                $this->getPhonePePayUrl(),
                $payload
            );

            Log::info('PHONEPE CREATE RESPONSE', [
                'request' => $payload,
                'status'  => $response->status(),
                'headers' => $response->headers(),
                'body'    => $response->body(),
                'json'    => $response->json(),
            ]);

            if (!$response->successful()) {

                return response()->json([
                    'success' => false,
                    'message' => $response->json('message')
                        ?? 'PhonePe payment creation failed'
                ], 500);
            }

            $data = $response->json();

            PhonepeLog::create([
                'offer_type'   => $order->offer_type,
                'order_id'     => $order->id,
                'order_amount' => $order->amount,
                'reference_id' => $merchantOrderId,
                'tx_status'    => 'pending',
                'service_type' => $request->service_code,
            ]);

            Log::info('PHONEPE PAYMENT CREATED', [
                'order_id' => $order->id,
                'merchant_order_id' => $merchantOrderId,
                'payment_url' => $data['redirectUrl'] ?? $data['data']['redirectUrl'] ?? null,
            ]);

            return response()->json([
                'success'     => true,
                'type'        => 'phonepe',
                'payment_url' => $data['redirectUrl']
                    ?? $data['data']['redirectUrl']
                    ?? null,
                'order_id'    => $merchantOrderId,
            ]);
        } catch (\Exception $e) {

            Log::error('PHONEPE PAYMENT ERROR', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment initiation failed'
            ], 500);
        }
    }

    public function phonepeVerify(Request $request, BrevoMailService $brevoMailService)
    {
        $orderId = $request->query('order_id');

        if (!$orderId) {
            return redirect(config('services.app.frontend_url') . '/staroffer');
        }

        $log = PhonepeLog::where('reference_id', $orderId)->first();

        if (!$log) {
            return redirect(config('services.app.frontend_url') . '/staroffer');
        }

        // Instant success (no API call)
        if ($log->tx_status === 'success') {
            return redirect(
                config('services.app.frontend_url') . '/staroffer-response?status=success'
            );
        }

        $accessToken = $this->getPhonepeAccessToken();

        if (!$accessToken) {
            return redirect(
                config('services.app.frontend_url') . '/staroffer-response?status=failed'
            );
        }

        $response = Http::withHeaders([
            'Authorization' => 'O-Bearer ' . $accessToken,
            'Accept' => 'application/json',
        ])->get($this->getPhonePeOrderStatusUrl($orderId));

        if (!$response->successful()) {
            return redirect(
                config('services.app.frontend_url') . '/staroffer-response?status=failed'
            );
        }

        $data = $response->json();

        $state = strtoupper(
            $data['state'] ?? $data['paymentDetails'][0]['state'] ?? 'PENDING'
        );

        $paymentId = $data['paymentDetails'][0]['transactionId'] ?? null;
        $paymentMode = $data['paymentDetails'][0]['paymentMode'] ?? null;

        $order = OfferOrder::find($log->order_id);

        if ($state === 'COMPLETED') {

            $order = OfferOrder::find($log->order_id);

            if ($order && !$order->payment_id) {
                $order->update([
                    'payment_id' => $paymentId,
                    'card_number' => generateCardNumber(),
                ]);
            }

            $log->update([
                'payment_id' => $paymentId,
                'tx_status' => 'success',
                'payment_mode' => $paymentMode,
            ]);

            try {
                $successPayment = view('emails.payment_success', [
                    'customer' => $order
                ])->render();

                $brevoMailService
                    ->sendBrevoHtmlMail(
                        $order->email,
                        $order->full_name,
                        'Payment Success',
                        $successPayment
                    );
            } catch (\Exception $e) {

                Log::error('EMAIL Success', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'customer_id' => $customer->id ?? null,
                    'invoice_id' => $invoice->id ?? null,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            return redirect(
                config('services.app.frontend_url') . '/staroffer-response?status=success'
            );
        }

        if (in_array($state, ['FAILED', 'FAILURE', 'CANCELLED'])) {

            $log->update([
                'tx_status' => 'failed',
            ]);

            try {
                $failePayment = view('emails.payment_failed', [
                    'customer' => $order
                ])->render();

                $brevoMailService
                    ->sendBrevoHtmlMail(
                        $order->email,
                        $order->full_name,
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

            return redirect(
                config('services.app.frontend_url') . '/staroffer'
            );
        }

        return redirect(
            config('services.app.frontend_url') . '/staroffer-response?status=failed'
        );
    }

    public function processPayu($order, Request $request)
    {
        $key = config('services.payu.key');
        $salt = config('services.payu.salt');

        $txnid = generateReferenceId();
        $amount = number_format((float) $order->amount, 2, '.', '');
        $productinfo = 'Bumper Offer';
        $firstname = trim($request->fullName);
        $email = trim($request->email);

        $udf1 = '';
        $udf2 = '';
        $udf3 = '';
        $udf4 = '';
        $udf5 = '';

        $hashString = implode('|', [
            $key,
            $txnid,
            $amount,
            $productinfo,
            $firstname,
            $email,
            $udf1,
            $udf2,
            $udf3,
            $udf4,
            $udf5,
        ]) . '||||||' . $salt;

        $hash = strtolower(hash('sha512', $hashString));

        try {
            PayuLog::create([
                'order_id' => (string) $order->id,
                'order_amount' => $amount,
                'order_note' => $productinfo,
                'reference_id' => $txnid,
                'payment_id' => null,
                'tx_status' => 'initiated',
                'payment_mode' => null,
                'service_type' => $request->service_code,
                'offer_type' => $order->offer_type,
            ]);

            // $order->payment_id = $txnid;
            // $order->save();
        } catch (\Throwable $e) {
            Log::error('PAYU INITIATION FAILED', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to initiate payment.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'gateway' => 'payu',
            'payment_url' => rtrim($this->getPayUBaseUrl(), '/') . '/_payment',
            'payment_data' => [
                'key' => $key,
                'txnid' => $txnid,
                'amount' => $amount,
                'productinfo' => $productinfo,
                'firstname' => $firstname,
                'email' => $email,
                'phone' => $order->mobile,
                'surl' => route('payu.callback'),
                'furl' => route('payu.callback'),
                'hash' => $hash,
            ],
        ]);
    }
    public function handlePayuCallback(Request $request)
    {
        $data = $request->all();
        $key = config('services.payu.key');
        $salt = config('services.payu.salt');
        $txnid = (string) ($data['txnid'] ?? '');

        if (
            !$key || !$salt || !$txnid ||
            empty($data['hash']) ||
            !isset(
                $data['status'],
                $data['amount'],
                $data['productinfo'],
                $data['firstname'],
                $data['email']
            ) ||
            !hash_equals($key, (string) ($data['key'] ?? ''))
        ) {
            return response('Invalid PayU response.', 400);
        }

        // Validate PayU's reverse response hash.
        $reverseHash = $salt . '|' .
            $data['status'] . '||||||' .
            ($data['udf5'] ?? '') . '|' .
            ($data['udf4'] ?? '') . '|' .
            ($data['udf3'] ?? '') . '|' .
            ($data['udf2'] ?? '') . '|' .
            ($data['udf1'] ?? '') . '|' .
            $data['email'] . '|' .
            $data['firstname'] . '|' .
            $data['productinfo'] . '|' .
            $data['amount'] . '|' .
            $txnid . '|' . $key;

        if (!hash_equals(
            strtolower(hash('sha512', $reverseHash)),
            strtolower((string) $data['hash'])
        )) {
            return response('Invalid PayU hash.', 400);
        }

        $log = PayuLog::where('reference_id', $txnid)->first();

        if (!$log) {
            return response('Transaction not found.', 404);
        }

        // Do not process a transaction already marked successful.
        if ($log->tx_status === 'success') {
            return redirect()->away(
                rtrim(config('app.frontend_url'), '/') . '/bumperoffer-response?status=success'
            );
        }

        $apiUrl = config('services.payu.mode') === 'production'
            ? 'https://info.payu.in/merchant/postservice.php?form=2'
            : 'https://test.payu.in/merchant/postservice.php?form=2';

        $verifyHash = strtolower(hash(
            'sha512',
            $key . '|verify_payment|' . $txnid . '|' . $salt
        ));

        try {
            $response = Http::asForm()->timeout(20)->post($apiUrl, [
                'key' => $key,
                'command' => 'verify_payment',
                'var1' => $txnid,
                'hash' => $verifyHash,
            ]);

            if (!$response->successful()) {
                return response('Payment verification pending.', 503);
            }

            $result = $response->json();
            // Log::info('PAYU VERIFY DEBUG', [
            //     'txnid' => $txnid,
            //     'http_status' => $response->status(),
            //     'api_status' => $result['status'] ?? null,
            //     'api_message' => $result['msg'] ?? null,
            //     'transaction' => $result['transaction_details'][$txnid] ?? null,
            //     'callback_amount' => $data['amount'] ?? null,
            //     'expected_amount' => $log->order_amount,
            // ]);
            $transaction = $result['transaction_details'][$txnid] ?? null;

            if (!is_array($transaction)) {
                return response('Transaction verification pending.', 503);
            }

            $verifiedAmount = $transaction['amt']
                ?? $transaction['transaction_amount']
                ?? null;

            $expectedAmount = number_format(
                (float) $log->order_amount,
                2,
                '.',
                ''
            );

            $isPaid = ($result['status'] ?? null) == 1
                && strtolower((string) ($transaction['status'] ?? '')) === 'success'
                && strtolower((string) ($transaction['unmappedstatus'] ?? '')) === 'captured'
                && $verifiedAmount !== null
                && (float) $verifiedAmount === (float) $expectedAmount
                && (float) $data['amount'] === (float) $expectedAmount;

            $log->payment_id = $transaction['mihpayid']
                ?? $data['mihpayid']
                ?? $txnid;

            $log->payment_mode = $transaction['mode']
                ?? $data['mode']
                ?? null;

            $log->tx_status = $isPaid
                ? 'success'
                : (
                    strtolower((string) ($transaction['status'] ?? '')) === 'failure'
                    ? 'failure'
                    : 'pending'
                );

            $log->save();

            if ($isPaid) {
                $offerOrder = OfferOrder::find($log->order_id);

                if ($offerOrder) {
                    $offerOrder->payment_id = $transaction['mihpayid']
                        ?? $data['mihpayid']
                        ?? null;
                    $offerOrder->card_number = generateCardNumber();
                    $offerOrder->save();
                }
            }

            $frontendUrl = rtrim(config('services.app.frontend_url'), '/');
            // Log::info('redirect url', [$frontendUrl . '/bumperoffer-response?status=' .
            //     ($isPaid ? 'success' : 'failure')]);

            return redirect()->away(
                $frontendUrl . '/bumperoffer-response?status=' .
                    ($isPaid ? 'success' : 'failure')
            );
        } catch (\Throwable $e) {
            Log::error('PAYU CALLBACK ERROR', [
                'txnid' => $txnid,
                'error' => $e->getMessage(),
            ]);

            return response('Payment verification pending.', 503);
        }
    }
}
