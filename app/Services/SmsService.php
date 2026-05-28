<?php

namespace App\Services;

use App\Models\SiteOption;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected string $url;
    protected string $username;
    protected string $password;
    protected string $senderId;

    public function __construct()
    {
        $this->url = config('services.sms.api_url');
        $this->username = config('services.sms.username');
        $this->password = config('services.sms.password');
        $this->senderId = SiteOption::where(
            'option_key',
            'sms-sender-id'
        )->value('option_value') ?? config('services.sms.sender_id');
    }

    // Send Single SMS
    public function send(
        string $mobile,
        string $message
    ): array {

        try {

            $response = Http::timeout(30)
                ->get($this->url, [
                    'user' => $this->username,
                    'password' => $this->password,
                    'senderid' => $this->senderId,
                    'mobiles' => $mobile,
                    'sms' => $message,
                ]);

            Log::info('SMS Sent', [
                'mobile' => $mobile,
                'message' => $message,
                'response' => $response->body(),
            ]);

            return [
                'success' => $response->successful(),
                'response' => $response->body(),
            ];
        } catch (\Exception $e) {

            Log::error('SMS Failed', [
                'mobile' => $mobile,
                'message' => $message,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'response' => $e->getMessage(),
            ];
        }
    }

    // Send Template SMS
    public function sendTemplate(
        string $mobile,
        string $templateKey,
        array $variables = []
    ): array {

        $template = SiteOption::where(
            'option_key',
            $templateKey
        )->value('option_value');

        if (!$template) {

            return [
                'success' => false,
                'response' => 'SMS template not found.',
            ];
        }

        foreach ($variables as $key => $value) {

            $template = str_replace(
                '{#var_' . $key . '#}',
                $value,
                $template
            );
        }

        return $this->send($mobile, $template);
    }

    // Send Bulk / Remarketing SMS
    public function sendBulk(
        array $mobiles,
        string $message
    ): array {

        try {

            $mobileList = implode(',', $mobiles);

            $response = Http::timeout(60)
                ->get($this->url, [
                    'user' => $this->username,
                    'password' => $this->password,
                    'senderid' => $this->senderId,
                    'mobiles' => $mobileList,
                    'sms' => $message,
                ]);

            Log::info('Bulk SMS Sent', [
                'mobiles' => $mobileList,
                'message' => $message,
                'response' => $response->body(),
            ]);

            return [
                'success' => $response->successful(),
                'response' => $response->body(),
            ];
        } catch (\Exception $e) {

            Log::error('Bulk SMS Failed', [
                'mobiles' => $mobiles,
                'message' => $message,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'response' => $e->getMessage(),
            ];
        }
    }

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
                'mobiles' => '91' . $mobileNumber, //  FIXED
                'sms' => $message
            ]);

            $result = $response->body();

            //  Debug safely
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

    public function smsMessage($slug)
    {
        try {

            $data = SiteOption::where('option_key', $slug)->first();

            if (!$data) {
                return [
                    'success' => false,
                    'message' => 'sms template not found',
                ];
            }

            // Log::info("message sms: ", [
            //     'slug' => $slug,
            //     "message" => $data->option_value
            // ]);

            return [
                'success' => true,
                'message' => $data->option_value
            ];
        } catch (\Exception $e) {

            // Log::error("sms message error: ", [
            //     'slug' => $slug,
            //     'error' => $e->getMessage()
            // ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
