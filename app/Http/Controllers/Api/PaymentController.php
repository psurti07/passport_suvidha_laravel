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

        // $amount = (int) $service->service_total_amount;

        // $testNumbers = explode(',', config('services.testnumbers.number', ''));

        // if (in_array($request->mobile, $testNumbers)) {
        //     $amount = 1;
        // }

        // $razorpayAmount = $amount * 100;

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
            'order_id' => round(microtime(true) * 1000), 
            'order_amount' => $finalAmount, 
            'order_note' => 'Passport Application',
            'reference_id' => $order['id'], 
            'tx_status' => 'pending',
            'payment_mode' => 'razorpay',
        ]);

        return response()->json([
            'id' => $order['id'],
            'amount' => $razorpayAmount
        ]);
    }
    public function verifyPayment(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

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

            //  Prevent duplicate payment
            if ($customer->is_paid == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment already completed'
                ], 400);
            }

            //  Fetch payment from Razorpay
            $payment = $api->payment->fetch($request->razorpay_payment_id);
            $paymentMode = $payment->method;

            DB::beginTransaction();

            //  Update log
            $log->update([
                'tx_status' => 'success',
                'payment_mode' => $paymentMode
            ]);

            $finalAmount = $log->order_amount;

            // ADD HERE
            if ($finalAmount == 1) {

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Test payment successful (₹1) - No order/invoice created'
                ]);
            }

            $order = ApplicationOrder::updateOrCreate(
                ['customer_id' => $log->customer_id],
                [
                    'customer_id' => $log->customer_id,
                    'card_number' => str_pad(mt_rand(0, 9999999999999999), 16, '0', STR_PAD_LEFT),
                    'amount' => $log->order_amount,
                    'payment_id' => $request->razorpay_payment_id,
                ]
            );

            $log->update([
                'order_id' => $order->id
            ]);

            $customer->update([
                'is_paid' => 1,
                'is_active' => 1
            ]);

            $service = DB::table('services')->where('id', $customer->service_id)->first();

            $govAmount = $service->service_gov_amount ?? 0;
            $serviceCharges = $service->service_charges ?? 0;

            $netAmount = $govAmount + $serviceCharges;

            $cgst = $sgst = $igst = 0;

            if ($customer->state == 'GJ') {
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
            $smsService = new SmsService();
            $mobileNumber = $customer->mobile_number;
            if (!empty($mobileNumber)) {

                    $message = "Congrats, Your Passport Application is submitted successfully! Our Company Executive will contact you within 24-48 hours to proceed. Thanks, PassportSuvidha";

                    $response = $smsService->sendSms($mobileNumber, $message);
                }

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('PAYMENT VERIFY ERROR: ' . $e->getMessage());

             $smsService = new SmsService();
            $mobileNumber = $customer->mobile_number;
            if (!empty($mobileNumber)) {

                    $message = "Sorry, your payment for Passport Consulting application was not successful. We request you to try another payment method {#var#} Passport Suvidha";

                    $response = $smsService->sendSms($mobileNumber, $message);
                }

            RazorpayLog::create([
                'customer_id' => auth()->id() ?? 0,
                'order_id' => time(),
                'order_amount' => 0,
                'order_note' => 'Passport Application',
                'reference_id' => $request->razorpay_order_id ?? 'unknown',
                'tx_status' => 'failed',
                'payment_mode' => 'razorpay',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
