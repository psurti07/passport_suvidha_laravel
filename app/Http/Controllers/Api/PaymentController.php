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

        $service = Service::where('service_code', $request->service_code)->first();

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
                    'is_active' => 1
                ]);

                DB::commit();

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
                'is_active' => 1
            ]);

            $service = $customer->service;

            $govAmount = $service->service_gov_amount ?? 0;
            $serviceCharges = $service->service_charges ?? 0;

            $netAmount = $govAmount + $serviceCharges;

            $cgst = $sgst = $igst = 0;

            if (strtoupper($customer->state) == 'GUJARAT') {
                $cgst = round($serviceCharges * 0.09, 2);
                $sgst = round($serviceCharges * 0.09, 2);
                $total = $netAmount + $cgst + $sgst;
            } else {
                $igst = round($serviceCharges * 0.18, 2);
                $total = $netAmount + $igst;
            }

            $invoice = Invoice::create([
                'customer_id' => $log->customer_id,
                'order_id' => $order->id,
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

            if (!empty($customer->mobile_number)) {
                $smsService = new SmsService();
                $smsMessage = $smsService->smsMessage('complete-process-sms');
                if (!$smsMessage['success']) {
                    return response([
                        'success' => false,
                        'message' => "SMS template not found"
                    ]);
                }

                $message = $smsMessage['message'];

                $smsService->sendSms($customer->mobile_number, $message);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('PAYMENT VERIFY ERROR: ' . $e->getMessage());

            $paymentMode = null;

            if (!empty($request->razorpay_payment_id)) {
                try {
                    $payment = $api->payment->fetch($request->razorpay_payment_id);
                    $paymentMode = $payment->method;
                } catch (\Exception $ex) {
                    Log::error('PAYMENT FETCH ERROR: ' . $ex->getMessage());
                }
            }

            $log = RazorpayLog::where('reference_id', $request->razorpay_order_id)->first();

            if ($log) {
                $log->update([
                    'tx_status' => 'failed',
                    'payment_id' => $request->razorpay_payment_id ?? null,
                    'payment_mode' => $paymentMode
                ]);
            }

            if (isset($customer) && !empty($customer->mobile_number)) {
                $smsService = new SmsService();

                $smsMessage = $smsService->smsMessage('payment-failed-sms');

                if (!$smsMessage['success']) {
                    return response([
                        'success' => false,
                        'message' => "SMS template not found"
                    ]);
                }

                $message = str_replace('{#var#}', $paymentMode ?? 'UPI', $smsMessage['message']);

                $smsService->sendSms($customer->mobile_number, $message);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed'
            ], 400);
        }
    }

    public function paymentFailed(Request $request)
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
            if ($customer && !empty($customer->mobile_number)) {

                $mobileNumber = $customer->mobile_number;

                $smsService = new SmsService();
                $smsMessage = $smsService->smsMessage('payment-failed-sms');

                if (!$smsMessage['success']) {
                    return response([
                        'success' => false,
                        'message' => "SMS template not found"
                    ]);
                }

                $message = str_replace('{#var_method#}', $paymentMode ?? '', $smsMessage['message']);
                $smsService->sendSms($mobileNumber, $message);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment marked as failed'
        ]);
    }
}
