<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\OfferOrder;
use App\Models\CashfreeLogsEntry;
use Illuminate\Support\Str;

class OfferOrderController extends Controller
{

    public function createOrder(Request $request)
    {
        $request->validate([
            'fullName' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
        ]);

        $testNumbers = explode(',', env('TEST_NUMBERS', ''));

        $amount = 199;

        if (in_array($request->mobile, $testNumbers)) {
            $amount = 1;
        }

        $order = OfferOrder::create([
            'full_name' => $request->fullName,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'amount' => $amount,
        ]);

        $orderId = "order_" . $order->id . "_" . Str::random(6);

        $response = Http::withHeaders([
            'x-client-id' => env('CASHFREE_APP_ID'),
            'x-client-secret' => env('CASHFREE_SECRET_KEY'),
            'x-api-version' => '2022-09-01',
            'Content-Type' => 'application/json',
        ])->post('https://api.cashfree.com/pg/order', [
            "order_id" => $orderId,
            "order_amount" => $amount,
            "order_currency" => "INR",
            "customer_details" => [
                "customer_id" => (string)$order->id,
                "customer_name" => $request->fullName,
                "customer_email" => $request->email,
                "customer_phone" => $request->mobile,
            ]
        ]);

        if ($response->successful()) {

            $orderLogId = round(microtime(true) * 1000);

            CashfreeLogsEntry::create([
                'customer_id' => $order->id,
                'order_id' => $orderLogId,
                'order_amount' => $amount,
                'order_note' => 'Passport Application',
                'reference_id' => $orderId,
                'tx_status' => 'pending',
                'payment_mode' => 'cashfree',
            ]);

            return response()->json([
                "success" => true,
                "payment_session_id" => $response['payment_session_id'],
                "order_id" => $orderId
            ]);
        }

        return response()->json([
            "success" => false,
            "message" => "Payment initiation failed"
        ]);
    }

   public function paymentSuccess(Request $request)
    {
        $orderId = $request->order_id;

        preg_match('/order_(\d+)_/', $orderId, $matches);

        if (!isset($matches[1])) {
            return response()->json(["success" => false]);
        }

        $order = OfferOrder::find($matches[1]);

        if (!$order) {
            return response()->json(["success" => false]);
        }

        $log = CashfreeLogsEntry::where('reference_id', $orderId)->first();

        $response = Http::withHeaders([
            'x-client-id' => env('CASHFREE_APP_ID'),
            'x-client-secret' => env('CASHFREE_SECRET_KEY'),
            'x-api-version' => '2022-09-01',
        ])->get('https://sandbox.cashfree.com/pg/orders/' . $orderId . '/payments');

        if ($response->successful()) {

            $payments = $response->json();

            if (!empty($payments)) {

                //  Find SUCCESS payment
                $payment = collect($payments)
                    ->firstWhere('payment_status', 'SUCCESS');

                if ($payment) {

                    //  Extract payment ID
                    $paymentId = $payment['cf_payment_id'];

                    //  Extract card number (if exists)
                    $cardNumber = null;

                    if (isset($payment['payment_method']['card']['card_number'])) {
                        $cardNumber = $payment['payment_method']['card']['card_number'];
                    }

                    //  Update offer_orders
                    $order->update([
                        'payment_id' => $paymentId,
                        'card_number' => $cardNumber,
                    ]);

                    //  Update logs
                    if ($log) {
                        $log->update([
                            'tx_status' => 'success',
                            'payment_mode' => $payment['payment_group'] ?? 'unknown'
                        ]);
                    }

                    return response()->json(["success" => true]);
                }

                // If not success → failed or pending
                $latest = end($payments);

                if ($log) {
                    $log->update([
                        'tx_status' => strtolower($latest['payment_status']),
                        'payment_mode' => $latest['payment_group'] ?? 'unknown'
                    ]);
                }
            }
        }

        return response()->json(["success" => false]);
    }

    public function checkPaymentStatus(Request $request)
    {
        $orderId = $request->order_id;

        $log = CashfreeLogsEntry::where('reference_id', $orderId)->first();

        $response = Http::withHeaders([
            'x-client-id' => env('CASHFREE_APP_ID'),
            'x-client-secret' => env('CASHFREE_SECRET_KEY'),
            'x-api-version' => '2022-09-01',
        ])->get("https://sandbox.cashfree.com/pg/orders/{$orderId}/payments");

        if ($response->successful()) {

            $payments = $response->json();

            if (!empty($payments)) {

                $latest = end($payments);
                $status = $latest['payment_status'];

                if ($log) {
                    $log->update([
                        'tx_status' => strtolower($status),
                        'payment_mode' => $latest['payment_group'] ?? 'unknown'
                    ]);
                }

                return response()->json([
                    "status" => $status
                ]);
            }
        }

        return response()->json([
            "status" => "PENDING"
        ]);
    }
}
