<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\RazorpayLogsEntry;

class PaymentController extends Controller
{
    // 🟡 Create Order
    public function createOrder(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $order = $api->order->create([
            'receipt' => 'order_rcptid_' . time(),
            'amount' => $request->amount,
            'currency' => 'INR'
        ]);

         RazorpayLogsEntry::create([
        'entryfor' => 1, // Customer
        'userid' => auth()->id() ?? 0,
        'orderid' => time(), // your internal order id (or pass from frontend)
        'orderamount' => $request->amount,
        'ordernote' => 'Passport Application',
        'referenceid' => $order['id'], // Razorpay Order ID
        'txstatus' => 'pending',
        'paymentmode' => 'razorpay',
    ]);

         return response()->json($order->toArray());
    }

    // 🟢 Verify Payment
    public function verifyPayment(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        try {
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // ✅ Payment is verified → Save in DB
            // Example:
            // Payment::create([...]);

            $log = RazorpayLogsEntry::where('referenceid', $request->razorpay_order_id)->first();

            if (!$log) {
                return response()->json([
                    'success' => false,
                    'message' => 'Log not found'
                ]);
            }

            // ✅ ONLY update status
            $log->update([
                'txstatus' => 'success',
                'paymentmode' => 'upi'
            ]);

            // ✅ Update customer
            Customer::where('id', $log->userid)->update([
                'is_paid' => 1
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment verified'
            ]);
        } catch (\Exception $e) {
        
          RazorpayLogsEntry::create([
            'entryfor' => 1,
            'userid' => auth()->id() ?? 0,
            'orderid' => time(),
            'orderamount' => 0,
            'ordernote' => 'Passport Application',
            'referenceid' => $request->razorpay_order_id,
            'txstatus' => 'failed',
            'paymentmode' => 'razorpay',
        ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid signature'
            ], 400);
        }
    }
}
