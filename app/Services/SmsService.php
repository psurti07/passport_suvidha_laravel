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
        $this->url = 'http://m.onlinebusinessbazaar.in/sendsms.jsp';
        $this->username = SiteOption::getValue('sms-user-name');
        $this->password = SiteOption::getValue('sms-password');
        $this->senderId = SiteOption::getValue('sms-sender-id');
    }

    public function sendSmsMessage(
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

    public function sendTemplateSms(
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

        foreach ($variables as $value) {
            $template = preg_replace(
                '/\{#var#\}/',
                $value,
                $template,
                1
            );
        }

        return $this->sendSmsMessage($mobile, $template);
    }

    public function sendBulkSms(
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
}
