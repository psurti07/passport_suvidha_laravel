<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\SmsService;

class OtpController extends Controller
{
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|string|min:10|max:15',
            'purpose' => 'required|in:registration,login',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $mobileNumber = $validated['mobile_number'];
        $purpose = $validated['purpose'];

        $customer = Customer::where('mobile_number', $mobileNumber)->first();

        if ($purpose === 'login' && !$customer) {
            return response()->json(['errors' => ['mobile_number' => ['Customer not found with this mobile number.']]], 404);
        }

        if ($purpose === 'registration' && !$customer) {
            return response()->json(['errors' => ['mobile_number' => ['Complete basic information first.']]], 422);
        }

        $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        
        Otp::create([
            'mobile_number' => $mobileNumber,
            'otp' => $otp,
            'sent_at' => Carbon::now(),
            'purpose' => $purpose
        ]);

        if ($purpose === 'login') {
            $message = "Hello, {$otp} is the OTP for logging into your Passport Suvidha account. Please don't share this with others. Thank you.";
        } else {
            $message = "Hello, {$otp} is the OTP for registering with Passport Suvidha. Please don't share this with others. Thank you.";
        }

        $smsResult = $this->sendSms($mobileNumber, $message);
        
        if (!$smsResult['success']) {
            Log::error('Failed to send OTP SMS', [
                'mobile' => $mobileNumber,
                'error' => $smsResult['error'] ?? 'Unknown error'
            ]);                        
        }
        
        $response = [
            'message' => 'OTP sent successfully.',
            'mobile_number' => $mobileNumber,
            'purpose' => $purpose,
            'expires_in' => 10, // minutes            
        ];
        
        if (isset($smsResult) && !$smsResult['success']) {
            $response['warning'] = 'We had trouble sending the OTP. If you did not receive it, please try again later.';
        }
        
        return response()->json($response);
    }

    /**
     * Send SMS using the external SMS API
     * 
     * @param string $mobileNumber
     * @param string $message
     * @return array
     */

    private function sendSms($mobileNumber, $message)
    {
        try {
            $username = config('services.sms.username');
            $password = config('services.sms.password');
            $senderId = config('services.sms.sender_id');
            
            $url = "http://m.onlinebusinessbazaar.in/sendsms.jsp";
            
            $response = Http::get($url, [
                'user' => $username,
                'password' => $password,
                'senderid' => $senderId,
                'mobiles' => '91' . $mobileNumber, 
                'sms' => $message
            ]);
            
            $result = $response->body();

            Log::info('SMS DEBUG', [
                'response' => $result,
                'username' => $username,
                'senderId' => $senderId
            ]);

            return [
                'success' => !str_contains(strtolower($result), 'error'),
                'response' => $result
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|string|min:10|max:15',
            'otp' => 'required|string|digits:4',
            'purpose' => 'required|in:registration,login',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $mobileNumber = $validated['mobile_number'];
        $inputOtp = $validated['otp'];
        $purpose = $validated['purpose'];

        $customer = Customer::where('mobile_number', $mobileNumber)->first();

        if (!$customer) {
            return response()->json(['errors' => ['mobile_number' => ['Customer not found.']]], 404);
        }

        $otpRecord = Otp::where('mobile_number', $mobileNumber)
            ->where('otp', (int) $inputOtp)
            ->where('purpose', $purpose)
            ->where('is_verified', false)
            ->where('sent_at', '>=', Carbon::now()->subMinutes(10))
            ->latest('sent_at')
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'errors' => ['otp' => ['Invalid or expired OTP. Please request a new one.']]
            ], 401);
        }

        $otpRecord->update(['is_verified' => true]);


        if ($purpose === 'login') {

            if ($customer->registration_step < 4) {
                return response()->json([
                    'errors' => ['registration' => ['Please complete your registration process first.']]
                ], 422);
            }
            
            $customer->tokens()->delete();
            
            $token = $customer->createToken('customer-login-token')->plainTextToken;
            
            return response()->json([
                'message' => 'Login successful.',
                'customer' => $customer,
                'token' => $token,
                'token_type' => 'Bearer'
            ]);
        }
    
        if ($purpose === 'registration') {

            if ($customer->registration_step < 2) {
                $customer->update(['registration_step' => 2]);
            }
            
            $customer->tokens()->delete();

            $token = $customer->createToken('customer-registration-token')->plainTextToken;
            
            return response()->json([
                'message' => 'OTP verified successfully.',
                'customer' => $customer,
                'token' => $token,
                'registration_step' => $customer->registration_step,
                'next_step' => $this->getNextStep($customer->registration_step),
                'token_type' => 'Bearer',
            ]);
        }
    }

    private function getNextStep($step)
    {
        return match ($step) {
            1 => 'otp_verification',
            2 => 'additional_information',
            3 => 'service_selection',
            4 => 'payment',
            default => 'start',
        };

    }
}
