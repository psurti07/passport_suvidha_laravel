<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\OfferOrder;
use App\Models\CashfreeLog;
use App\Models\ZaakpayLog;
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
            'offer_type' => 'required'
        ]);

        $gateway = $this->getGatewayByOffer($request->offer_type);

        $offer_type = $request->offer_type;
        if($offer_type === 'card_offer') {
            $offer_type = 1;
        } elseif ($offer_type === 'star_offer') {
            $offer_type = 2;
        } 

        $order = OfferOrder::create([
            'full_name'   => $request->fullName,
            'mobile'      => $request->mobile,
            'email'       => $request->email,
            'offer_type'  => $offer_type, 
            'amount'      => 199,
        ]);
        Log::info("order data: ", ['order' => $order->toArray()]);

        return $this->processPayment($gateway, $order, $request);
    }

    private function getGatewayByOffer($offerType)
    {
        return match ($offerType) {
            'card_offer' => 'cashfree',
            'star_offer' => 'zaakpay',
            default => 'cashfree',
        };
    }

    private function processPayment($gateway, $order, $request)
    {
        switch ($gateway) {
            case 'cashfree':
                return $this->processCashfree($order, $request);

            case 'zaakpay':
                return $this->processZaakpay($order, $request);

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
                "notify_url" => config("services.app.url") . "/api/cashfree/webhook",
                // "notify_url" => "https://amplifier-shower-blemish.ngrok-free.dev/api/cashfree/webhook",

            ]
        ]);

        // if (!$response->successful()) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => $response->body()
        //     ]);
        // }

        CashfreeLog::create([
            // 'customer_id'  => $order->id,
            'offer_type' => 1,
            'order_id'     => $order->id,
            'order_amount' => $order->amount,
            'order_note'   => 'Card Offer Payment', 
            'reference_id' => $orderId,
            'tx_status'    => 'pending',
            'payment_mode' => 'cashfree', 
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
        $data = $request->all();

        Log::info("Cashfree webhook received", $data);

        // ✅ SAFE extraction
        $orderId = $data['data']['order']['order_id']
            ?? $data['order']['order_id']
            ?? null;

        if (!$orderId) {
            return response()->json(['status' => 'invalid']);
        }


        Log::info("Payment Status RAW", [
            "payment_status" => $data['data']['payment']['payment_status']
                ?? $data['payment']['payment_status']
                ?? null
        ]);

        // 🔥 Get payment status safely
        $paymentStatus = $data['data']['payment']['payment_status']
            ?? $data['payment']['payment_status']
            ?? null;

        $paymentStatus = strtoupper($paymentStatus);

        // 🔍 Find log directly (DO NOT use regex extraction)
        $log = CashfreeLog::where('reference_id', $orderId)->first();

        if (!$log) {
            Log::error("Cashfree log not found", ['order_id' => $orderId]);
            return response()->json(['status' => 'not_found']);
        }

        // =========================
        // SUCCESS
        // =========================
        if ($paymentStatus === 'SUCCESS') {

            $paymentMode = $data['data']['payment']['payment_group']
                ?? $data['payment']['payment_group']
                ?? 'unknown';

            $cfPaymentId = $data['data']['payment']['cf_payment_id']
                ?? $data['payment']['cf_payment_id']
                ?? null;

            $order = OfferOrder::find($log->order_id);

            if ($order) {
                $order->update([
                    'payment_id' => $cfPaymentId,
                    'card_number' => generateCardNumber(),
                ]);
            }

            $log->update([
                'payment_id' => $cfPaymentId,
                'tx_status' => 'success',
                'payment_mode' => $paymentMode
            ]);

            return response()->json(['status' => 'ok']);
        }

        // =========================
        // FAILED
        // =========================
        if ($paymentStatus === 'FAILED') {
                    Log::info("failed");
            $log->update([
                'tx_status' => 'failed'
            ]);

            return response()->json(['status' => 'ok']);
        }

        // =========================
        // DEFAULT
        // =========================
        return response()->json(['status' => 'pending']);
    }

    /*
    |--------------------------------------------------------------------------
    | ZAAKPAY
    |--------------------------------------------------------------------------
    */
    public function processZaakpay($order, $request)
    {
        try {
            $merchantId = config('services.zaakpay.merchant_identifier');
            $secret = hex2bin(config('services.zaakpay.secret_key'));

            $orderId = "ORD" . $order->id . time();

            // $returnUrl =  'https://amplifier-shower-blemish.ngrok-free.dev/api/zaakpay/callback';
            $returnUrl =  config("services.app.url") .'/api/zaakpay/callback';

            $queryString = implode('|', [
                $merchantId,                        
                $orderId,                           
                (int) ($order->amount * 100),       
                "INR",                              
                $returnUrl,                         
                $request->email,                    
                $request->fullName,                 
                "NA",                               
                "NA",                               
                "NA",                               
                "NA",                               
                "India",                            
                "395006",                           
                $request->mobile                    
            ]);

            Log::info("ZAAKPAY QUERY STRING", ['queryString' => $queryString]);

            $encRequest = base64_encode(openssl_encrypt(
                $queryString,
                'AES-128-ECB',
                $secret,
                OPENSSL_RAW_DATA
            ));

            $checksum = hash_hmac('sha256', $encRequest, $secret);

            ZaakpayLog::create([
                'order_note'    => 'star offer page',
                'offer_type'    => 2,
                'order_amount'  => 199,
                'order_id'      => $order->id,
                'reference_id'  => $orderId,
                'tx_status'     => 'pending'
            ]);

            return response()->json([
                "success" => true,
                "order_id" => $orderId,
                "payment_url" => $this->getZaakpayUrl(),
                "encRequest" => $encRequest,
                "merchantIdentifier" => $merchantId,
                "checksum" => $checksum
            ]);

        } catch (\Exception $e) {

            Log::error("Zaakpay Error", [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function zaakpayCallback(Request $request)
    {
        Log::info("Zaakpay Callback RAW", $request->all());

        $encResponse = $request->input('encResponse');

        if (!$encResponse) {
            return response()->json([
                "status" => "failed",
                "message" => "Invalid response"
            ]);
        }

        $decrypted = $this->decryptZaakpay($encResponse);
        parse_str($decrypted, $response);

        $orderId = $response['orderId'] ?? null;

        if (!$orderId) {
            return response()->json([
                "status" => "failed",
                "message" => "Order not found"
            ]);
        }

        $log = ZaakpayLog::where('reference_id', $orderId)->first();

        if (!$log) {
            return response()->json([
                "status" => "failed",
                "message" => "Log not found"
            ]);
        }

        $order = OfferOrder::find($log->order_id);

        if (($response['responseCode'] ?? '') == '100') {

            $order->update([
                'card_number' => generateCardNumber(),
                'payment_id' => $response['pgTransId'] ?? null
            ]);

            $log->update([
                'payment_id' => $response['pgTransId'] ?? null,
                'tx_status' => 'success',
            ]);

            return response()->json([
                "status" => "success",
                "message" => "Payment successful"
            ]);
        }

        $log->update(['tx_status' => 'failed']);

        return response()->json([
            "status" => "failed",
            "message" => "Payment failed"
        ]);
    }

    private function getZaakpayUrl()
    {
        return 'https://zaakstaging.zaakpay.com/api/paymentTransact/V8';
    }

    private function decryptZaakpay($data)
    {
        $secret = config('services.zaakpay.secret_key');

        $decrypted = openssl_decrypt(
            base64_decode($data),
            'AES-128-ECB',
            $secret,
            OPENSSL_RAW_DATA
        );

        return rtrim($decrypted, "\x00..\x1F"); // clean padding
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

    public function checkPaymentStatus(Request $request)
    {
        Log::info('ORDER ID:', [$request->order_id]);
        $orderId = $request->order_id;

        $log = CashfreeLog::where('reference_id', $orderId)->first();

        if (!$log) {
            return response()->json([
                "status" => "failed"
            ]);
        }

        // ✅ 1. TRUST DATABASE FIRST (from webhook)
        if ($log->tx_status === 'success') {
            return response()->json([
                "status" => "success"
            ]);
        }

        if ($log->tx_status === 'failed') {
            return response()->json([
                "status" => "failed"
            ]);
        }

        // ✅ 2. FALLBACK: CASHFREE API CHECK
        $response = Http::withHeaders([
            'x-client-id' => env('CASHFREE_APP_ID'),
            'x-client-secret' => env('CASHFREE_SECRET_KEY'),
            'x-api-version' => '2022-09-01',
        ])->get("https://api.cashfree.com/pg/orders/" . $orderId . "/payments");

        if ($response->successful()) {

            $payments = $response->json();

            if (!empty($payments)) {

                $latest = end($payments);
                $status = strtolower($latest['payment_status'] ?? 'pending');

                if ($status === 'success') {

                    $log->update([
                        'tx_status' => 'success',
                        'payment_mode' => $latest['payment_group'] ?? 'unknown'
                    ]);

                    return response()->json([
                        "status" => "success"
                    ]);
                }

                if ($status === 'failed') {

                    $log->update([
                        'tx_status' => 'failed'
                    ]);

                    return response()->json([
                        "status" => "failed"
                    ]);
                }
            }
        }

        return response()->json([
            "status" => "pending"
        ]);
    }

}
