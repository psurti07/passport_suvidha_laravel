<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class BrevoMailService
{
    public function sendBrevoHtmlMail(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlContent
    ): array {

        $payload = [
            'sender' => [
                'name'  => config('services.brevo.sender_name'),
                'email' => config('services.brevo.sender_email'),
            ],

            'to' => [
                [
                    'email' => $toEmail,
                    'name'  => $toName,
                ]
            ],

            'subject' => $subject,
            'htmlContent' => $htmlContent,
        ];

        return $this->sendRequest($payload);
    }

    public function sendBrevoHtmlMailWithAttachments(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlContent,
        array $attachments
    ): array {

        $payload = [
            'sender' => [
                'name'  => config('services.brevo.sender_name'),
                'email' => config('services.brevo.sender_email'),
            ],

            'to' => [
                [
                    'email' => $toEmail,
                    'name'  => $toName,
                ]
            ],

            'subject' => $subject,
            'htmlContent' => $htmlContent,
            'attachment' => $attachments,
        ];

        return $this->sendRequest($payload);
    }

    private function sendRequest(array $payload): array
    {
        try {

            $http = Http::timeout(30);

            if (app()->isLocal()) {
                $http = $http->withoutVerifying();
            }

            $response = $http
                ->withHeaders([
                    'accept'       => 'application/json',
                    'content-type' => 'application/json',
                    'api-key'      => config('services.brevo.api_key'),
                ])
                ->post(
                    'https://api.brevo.com/v3/smtp/email',
                    $payload
                );

            if ($response->successful()) {

                return [
                    'success' => true,
                    'data'    => $response->json(),
                ];
            }

            Log::error('Brevo Mail Failed', [
                'status'   => $response->status(),
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => $response->body(),
            ];
        } catch (\Exception $e) {

            Log::error('Brevo Mail Exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
