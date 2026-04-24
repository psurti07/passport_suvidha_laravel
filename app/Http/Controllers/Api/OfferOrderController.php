<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\OfferOrder;
use App\Models\CashfreeLog;
use App\Models\Customer;
use App\Models\ZaakpayLog;
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
            'offer_type' => 'required'
        ]);

        $customer = Customer::where('mobile_number',$request->mobile)->first();
        if($customer && $customer->is_paid === true){
            return response()->json([
                "success" => false,
                "message"=>"You are already registered customer"
            ],200);
        }

        $gateway = $this->getGatewayByOffer($request->offer_type);

        $offer_map = [
            'card_offer'=>1,
            'star_offer'=>2,
        ];

        $offer_type = $offer_map[$request->offer_type]??null;
        if(!$offer_type){
            return response()->json([
                "success"=>false,
                "message"=>"Invalid offer type",
            ],422);
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

        $amount = 199;
        $finalAmount = floor($amount);

        $testNumbers = array_map('trim', explode(',', config('services.testnumbers.number', '')));

        if (in_array($request->mobile, $testNumbers)) {
            $finalAmount = 1;
        }

        $order = OfferOrder::create([
            'full_name'   => $request->fullName,
            'mobile'      => $request->mobile,
            'email'       => $request->email,
            'offer_type'  => $offer_type, 
            'amount'      => $finalAmount,
        ]);

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
                "notify_url" => $this->getWebhookUrl(). '/api/cashfree/webhook',
            ]
        
        ]);
        if(!$response->successful()){       
            return response()->json([
                "success"=>false,
                "message"=>'payment gateway failed'
            ],200);
        }

        CashfreeLog::create([
            // 'customer_id'  => $order->id,
            'offer_type' => 1,
            'order_id'     => $order->id,
            'order_amount' => $order->amount,
            'order_note'   => 'Card Offer Payment', 
            'reference_id' => $orderId,
            'tx_status'    => 'pending',
            'payment_mode'=> 'cashfree'
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

        $orderId = $data['data']['order']['order_id']
            ?? $data['order']['order_id']
            ?? null;

        if (!$orderId) {
            return response()->json(['status' => 'invalid']);
        }

        $paymentStatus = $data['data']['payment']['payment_status']
            ?? $data['payment']['payment_status']
            ?? null;

        $paymentStatus = strtoupper($paymentStatus);

        $log = CashfreeLog::where('reference_id', $orderId)->first();

        if (!$log) {
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

            if ($paymentStatus === 'SUCCESS') {

                if ($log->tx_status === 'success') {
                    return response()->json(['status' => 'ok']);
                }

                $paymentMode = $data['data']['payment']['payment_group']
                    ?? $data['payment']['payment_group']
                    ?? 'unknown';

                $cfPaymentId = $data['data']['payment']['cf_payment_id']
                    ?? $data['payment']['cf_payment_id']
                    ?? null;

                $order = OfferOrder::find($log->order_id);

                if ($order && !$order->payment_id) {
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
        if (in_array($paymentStatus, ['FAILED', 'NOT_ATTEMPTED', 'USER_DROPPED', 'CANCELLED'])) {

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

    private function getWebhookUrl()
    {
        if (config('services.app.env') === 'local') {
            return config('services.app.ngrok_url');
        }
 
        return config('services.app.url');
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
            $baseUrl = $this->getWebhookUrl();

            // $returnUrl =  config("services.app.url") .'/api/zaakpay/callback';
            $returnUrl =  $baseUrl.'/api/zaakpay/callback';

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

            $encRequest = base64_encode(openssl_encrypt(
                $queryString,
                'AES-128-ECB',
                $secret,
                OPENSSL_RAW_DATA
            ));

            $checksum = hash_hmac('sha256', $encRequest, $secret);

            $amount = 199;
            $finalAmount = floor($amount);

            $testNumbers = explode(',', config('services.testnumbers.number', ''));

            if (in_array($request->mobile, $testNumbers)) {
                $finalAmount = 1;
            }

            ZaakpayLog::create([
                'order_note'    => 'star offer page',
                'offer_type'    => 2,
                'order_amount'  => $finalAmount,
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

            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function zaakpayCallback(Request $request)
    {
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
        $request->validate([
            'order_id' => 'required'
        ]);

        $log = CashfreeLog::where('reference_id', $request->order_id)->first();

        if (!$log) {
            return response()->json(['status' => 'not_found']);
        }

        if ($log->tx_status !== 'pending') {
            return response()->json(['status' => $log->tx_status]);
        }

        $baseUrl = $this->getCashfreeBaseUrl();

        $response = Http::withHeaders([
            'x-client-id' => config('services.cashfree.key'),
            'x-client-secret' => config('services.cashfree.secret'),
            'x-api-version' => '2022-09-01',
        ])->get($baseUrl . "/orders/{$request->order_id}/payments");

        if (!$response->successful()) {
            return response()->json(['status' => 'pending']);
        }

        $payments = $response->json();

        if (empty($payments)) {
            return response()->json(['status' => 'pending']);
        }

        $payment = collect($payments)->last();

        $status = strtoupper($payment['payment_status'] ?? 'PENDING');

        if ($status === 'SUCCESS') {

            $order = OfferOrder::find($log->order_id);

            if ($order && !$order->payment_id) {
                $order->update([
                    'payment_id' => $payment['cf_payment_id'] ?? null,
                    'card_number' => generateCardNumber(),
                ]);
            }

            $log->update([
                'payment_id' => $payment['cf_payment_id'] ?? null,
                'tx_status' => 'success',
            ]);

            return response()->json(['status' => 'success']);
        }

        if (in_array($status, ['FAILED', 'CANCELLED', 'USER_DROPPED'])) {

            $log->update([
                'tx_status' => 'failed'
            ]);

            return response()->json(['status' => 'failed']);
        }

        return response()->json(['status' => 'pending']);
    }

}
