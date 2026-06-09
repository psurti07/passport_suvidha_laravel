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
use App\Models\Service;
use App\Models\PhonepeLog;
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

        $customer = Customer::where('mobile_number', $request->mobile)->first();
        if ($customer && $customer->is_paid === true) {
            return response()->json([
                "success" => false,
                "message" => "You are already registered customer"
            ], 200);
        }

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
        ];

        $offer_type = $offer_map[$request->offer_type] ?? null;
        if (!$offer_type) {
            return response()->json([
                "success" => false,
                "message" => "Invalid offer type",
            ], 422);
        }

        $existingOrder = OfferOrder::where('mobile', $request->mobile)
            ->where('offer_type', $offer_type)
            ->whereNotNull('payment_id') // means already paid
            ->first();

        if ($existingOrder) {
            return response()->json([
                "success" => false,
                "message" => "You have already purchased this offer"
            ], 200);
        }

        $finalAmount = floor($amount);

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

    public function cashfreeWebhook(Request $request)
    {
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

    public function checkPaymentStatus(Request $request)
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

    /*
    |--------------------------------------------------------------------------
    | ENV
    |--------------------------------------------------------------------------
    */
    private function getCashfreeBaseUrl()
    {
        return config("services.cashfree.mode") === 'production'
            ? 'https://api.cashfree.com/pg'
            : 'https://sandbox.cashfree.com/pg';
    }

    /*
    |--------------------------------------------------------------------------
    | PHONEPE
    |--------------------------------------------------------------------------
    */
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

            $merchantOrderId =
                'order_' . strtoupper(Str::random(8));

            $finalAmount = floor($order->amount);

            $testNumbers = array_map('trim', explode(',', config('services.testnumbers.test_numbers', '')));

            if (in_array($request->mobile, $testNumbers)) {
                $finalAmount = 1;
            }

            $payload = [
                "merchantOrderId" => $merchantOrderId,
                "amount" => (int) round($finalAmount * 100),
                "expireAfter" => 1200,

                "metaInfo" => [
                    "udf1" => (string) $order->id,
                    "udf2" => (string) $request->mobile,
                ],

                "paymentFlow" => [
                    "type" => "PG_CHECKOUT",
                    "merchantUrls" => [
                        "redirectUrl" => config('services.app.frontend_url')
                            . '/staroffer-response'
                    ]
                ]
            ];

            // Log::info('PHONEPE CREATE REQUEST', $payload);

            $response = Http::withHeaders([
                'Authorization' => 'O-Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->post(
                $this->getPhonePePayUrl(),
                $payload
            );

            // Log::info('PHONEPE CREATE RESPONSE', [
            //     'request' => $payload,
            //     'status'  => $response->status(),
            //     'headers' => $response->headers(),
            //     'body'    => $response->body(),
            //     'json'    => $response->json(),
            // ]);

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
                'order_amount' => $finalAmount,
                'reference_id' => $merchantOrderId,
                'tx_status'    => 'pending',
                'service_type' => $request->service_code,
            ]);

            // Log::info('PHONEPE PAYMENT CREATED', [
            //     'order_id' => $order->id,
            //     'merchant_order_id' => $merchantOrderId,
            //     'payment_url' => $data['redirectUrl'] ?? $data['data']['redirectUrl'] ?? null,
            // ]);

            return response()->json([
                'success'     => true,
                'type'        => 'phonepe',
                'payment_url' => $data['redirectUrl']
                    ?? $data['data']['redirectUrl']
                    ?? null,
                'order_id'    => $merchantOrderId,
            ]);
        } catch (\Exception $e) {

            // Log::error('PHONEPE PAYMENT ERROR', [
            //     'message' => $e->getMessage()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment initiation failed'
            ], 500);
        }
    }

    public function checkPhonepeStatus(Request $request)
    {
        try {

            $request->validate([
                'order_id' => 'required'
            ]);

            $log = PhonepeLog::where('reference_id', $request->order_id)->first();

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

            $accessToken = $this->getPhonepeAccessToken();

            if (!$accessToken) {
                return response()->json([
                    'status' => 'pending'
                ]);
            }

            $response = Http::withHeaders([
                'Authorization' => 'O-Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ])->get(
                $this->getPhonePeOrderStatusUrl(
                    $request->order_id
                )
            );

            // Log::info('PHONEPE STATUS RESPONSE', [
            //     'status' => $response->status(),
            //     'body'   => $response->body(),
            // ]);

            if (!$response->successful()) {
                return response()->json([
                    'status' => 'pending'
                ]);
            }

            $data = $response->json();

            $state = strtoupper(
                $data['state']
                    ?? $data['paymentDetails'][0]['state']
                    ?? 'PENDING'
            );

            $paymentId = $data['paymentDetails'][0]['transactionId']
                ?? null;

            $paymentMode = $data['paymentDetails'][0]['paymentMode']
                ?? null;

            if ($state === 'COMPLETED') {

                $order = OfferOrder::find($log->order_id);

                if ($order && !$order->payment_id) {
                    $order->update([
                        'payment_id'  => $paymentId,
                        'card_number' => generateCardNumber(),
                    ]);
                }

                $log->update([
                    'payment_id'   => $paymentId,
                    'tx_status'    => 'success',
                    'payment_mode' => $paymentMode,
                ]);

                return response()->json([
                    'status' => 'success'
                ]);
            }

            if (
                in_array(
                    $state,
                    [
                        'FAILED',
                        'FAILURE',
                        'CANCELLED'
                    ]
                )
            ) {

                $log->update([
                    'tx_status' => 'failed',
                    'payment_id' => $paymentId,
                    'payment_mode' => $paymentMode,
                ]);

                return response()->json([
                    'status' => 'failed'
                ]);
            }

            return response()->json([
                'status' => 'pending'
            ]);
        } catch (\Exception $e) {

            Log::error('PHONEPE STATUS ERROR', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error'
            ], 500);
        }
    }

    public function phonepeRedirect(Request $request)
    {
        Log::info('PHONEPE REDIRECT', $request->all());

        return redirect(
            config('services.app.frontend_url')
                . '/staroffer-response'
        );
    }
}
