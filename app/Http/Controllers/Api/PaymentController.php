<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApplicationOrder;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\RazorpayLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\SmsService;

class PaymentController extends Controller
{

    public function createOrder(Request $request)
    {
        $request->validate([
            'service_code' => 'required',
            'mobile' => 'required'
        ]);

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        $service = DB::table('services')
            ->where('service_code', $request->service_code)
            ->first();

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid service'
            ], 400);
        }

        $amount = $service->service_total_amount;
        $finalAmount = floor($amount);

        $testNumbers = explode(',', config('services.testnumbers.number', ''));

        if (in_array($request->mobile, $testNumbers)) {
            $finalAmount = 1;
        }

        $razorpayAmount = $finalAmount * 100;

        $order = $api->order->create([
            'receipt' => 'order_' . time(),
            'amount' => $razorpayAmount,
            'currency' => 'INR'
        ]);

        // Store ONLY Razorpay order_id here
        RazorpayLog::create([
            'customer_id' => auth()->id() ?? 0,
            'order_id' => null, // will be updated later
            'payment_id' => null,
            'order_amount' => $finalAmount,
            'order_note' => 'Passport Application',
            'reference_id' => $order['id'], // Razorpay order_id
            'tx_status' => null, // no pending
            'payment_mode' => 'razorpay',
            'entry_for' => $request->service_code
        ]);

        return response()->json([
            'id' => $order['id'],
            'amount' => $razorpayAmount
        ]);
    }
    public function verifyPayment(Request $request)
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

            // Find log using Razorpay order_id
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

            // Prevent duplicate payment
            if ($customer->is_paid == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment already completed'
                ], 400);
            }

            // Fetch payment
            $payment = $api->payment->fetch($request->razorpay_payment_id);

            $paymentMode = $payment->method;

            DB::beginTransaction();

            $finalAmount = $log->order_amount;

            // Skip order creation for test ₹1
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

                return response()->json([
                    'success' => true,
                    'message' => 'Test payment successful (₹1)'
                ]);
            }

            // Create or update order
            $order = ApplicationOrder::updateOrCreate(
                ['customer_id' => $log->customer_id],
                [
                    'customer_id' => $log->customer_id,
                    'card_number' => generateCardNumber(),
                    'amount' => $log->order_amount,
                    'payment_id' => $request->razorpay_payment_id,
                ]
            );

            // Update log with FINAL values
            $log->update([
                'tx_status' => 'success',
                'payment_mode' => $paymentMode,
                'payment_id' => $request->razorpay_payment_id,
                'order_id' => $order->id
            ]);

            // Update customer
            $customer->update([
                'is_paid' => 1,
                'is_active' => 1
            ]);

            // Invoice logic
            $service = DB::table('services')->where('id', $customer->service_id)->first();

            $govAmount = $service->service_gov_amount ?? 0;
            $serviceCharges = $service->service_charges ?? 0;

            $netAmount = $govAmount + $serviceCharges;

            $cgst = $sgst = $igst = 0;

            if (strtoupper($customer->state) == 'GJ') {
                $cgst = round($serviceCharges * 0.09, 2);
                $sgst = round($serviceCharges * 0.09, 2);
                $total = $netAmount + $cgst + $sgst;
            } else {
                $igst = round($serviceCharges * 0.18, 2);
                $total = $netAmount + $igst;
            }

            $invoice = Invoice::create([
                'customer_id' => $log->customer_id,
                'card_id' => $order->id,
                'inv_date' => now(),
                'net_amount' => $netAmount,
                'cgst' => $cgst,
                'sgst' => $sgst,
                'igst' => $igst,
                'total_amount' => $total,
                'service_id' => $customer->service_id,
            ]);

            $invoice->update([
                'inv_no' => 'INV_' . $invoice->id
            ]);

            DB::commit();

            // SMS Success
            if (!empty($customer->mobile_number)) {
                $smsService = new SmsService();

                $message = "Congrats, Your Passport Application is submitted successfully! Our Company Executive will contact you within 24-48 hours to proceed. Thanks, PassportSuvidha";

                $smsService->sendSms($customer->mobile_number, $message);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('PAYMENT VERIFY ERROR: ' . $e->getMessage());

            // Update log as FAILED
            $log = RazorpayLog::where('reference_id', $request->razorpay_order_id)->first();

            if ($log) {
                $log->update([
                    'tx_status' => 'failed',
                    'payment_id' => $request->razorpay_payment_id ?? null
                ]);
            }

            // SMS Failure
            if (isset($customer) && !empty($customer->mobile_number)) {
                $smsService = new SmsService();

                $message = "Sorry, your payment failed. Please try again. Passport Suvidha";

                $smsService->sendSms($customer->mobile_number, $message);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed',
                // 'error' => $e->getMessage()
            ], 400);
        }
    }
    
}
// Cashfree Order Response: {
//     "cf_order_id": 2207239698,
//     "created_at": "2026-04-20T11:01:36+05:30",
//     "customer_details": {
//         "customer_id": "15",
//         "customer_name": "Ishita Ghanva",
//         "customer_email": "verloop.dev8@gmail.com",
//         "customer_phone": "7984756152",
//         "customer_uid": null
//     },
//     "entity": "order",
//     "order_amount": 199.0,
//     "order_currency": "INR",
//     "order_expiry_time": "2026-05-20T11:01:36+05:30",
//     "order_id": "order_15_oIFw2Q",
//     "order_meta": {
//         "return_url": null,
//         "notify_url": "https://amplifier-shower-blemish.ngrok-free.dev/api/cashfree/webhook",
//         "payment_methods": null
//     },
//     "order_note": null,
//     "order_splits": [],
//     "order_status": "ACTIVE",
//     "order_tags": null,
//     "payment_session_id": "session_Q64u7WSBSm8GQp6QjD4ejhMOyeat4mdJYArbSxfnLR0f8tLJSYuRJpagbC0jEsQtk0XvzJdCGyiJUiohMqoUHMrYgFToCduV1NhRgQRMBlUOZ_7JTPj3ZWZhpzr5Vgpaymentpayment",
//     "payments": {
//         "url": "https://sandbox.cashfree.com/pg/orders/order_15_oIFw2Q/payments"
//     },
//     "refunds": {
//         "url": "https://sandbox.cashfree.com/pg/orders/order_15_oIFw2Q/refunds"
//     },
//     "settlements": {
//         "url": "https://sandbox.cashfree.com/pg/orders/order_15_oIFw2Q/settlements"
//     },
//     "terminal_data": null
// }


