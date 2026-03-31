<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function sendSms($mobileNumber, $message)
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
                'mobiles' => '91' . $mobileNumber, // ✅ FIXED
                'sms' => $message
            ]);
            
            $result = $response->body();

            // ✅ Debug safely
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
}
?>