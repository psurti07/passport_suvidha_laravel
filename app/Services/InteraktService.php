<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SiteOption;

class InteraktService
{
    protected $baseUrl;
    protected $apiKey;
    protected $appointmentApiKey;
    protected $detailsVerificationApiKey;
    protected $templateName;
    protected $appointmentTemplateName;
    protected $detailsVerificationTemplateName;
    protected $mediaUrl;
    protected $appointmentMediaUrl;
    protected $detailsVerificationMediaUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://api.interakt.ai';
        $this->apiKey = SiteOption::getValue('interakt-key');
        $this->templateName = SiteOption::getValue('interakt-template-name');
        $this->mediaUrl = SiteOption::getValue('interakt-media-url');
        $this->appointmentApiKey = SiteOption::getValue('interakt-appointment-key');
        $this->appointmentTemplateName = SiteOption::getValue('interakt-appointment-template-name');
        $this->appointmentMediaUrl = SiteOption::getValue('interakt-appointment-media-url');
        $this->detailsVerificationApiKey = SiteOption::getValue('interakt-details-verification-key');
        $this->detailsVerificationTemplateName = SiteOption::getValue('interakt-details-verification-template-name');
        $this->detailsVerificationMediaUrl = SiteOption::getValue('interakt-details-verification-media-url');
    }

    public function send(string $mobile, string $name)
    {
        if (!$this->baseUrl || !$this->apiKey || !$this->templateName) {
            Log::error('INTERAKT CONFIG MISSING');
            return [
                'status' => false,
                'message' => 'Interakt config missing'
            ];
        }

        $payload = [
            "fullPhoneNumber" => '+91' . $mobile,
            "callbackData" => "some text here",
            "type" => "Template",
            "template" => [
                "name" => $this->templateName,
                "languageCode" => "en",
                "headerValues" => [
                    $this->mediaUrl
                ],
                "bodyValues" => [
                    $name
                ]
            ]
        ];

        try {

            $http = app()->environment('local')
                ? Http::withoutVerifying()
                : Http::acceptJson();

            $response = $http
                ->withHeaders([
                    'Authorization' => 'Basic ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post(
                    $this->baseUrl . '/v1/public/message/',
                    $payload
                );

            return [
                'status' => $response->successful(),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {

            Log::error('INTERAKT SEND ERROR : ' . $e->getMessage());

            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function sendDocument(string $mobile, string $name, string $documentUrl, string $templateType)
    {
        if ($templateType === 'appointment') {
            $apiKey = $this->appointmentApiKey;
            $templateName = $this->appointmentTemplateName;
            $mediaUrl = $this->appointmentMediaUrl;
        } elseif ($templateType === 'details_verification') {
            $apiKey = $this->detailsVerificationApiKey;
            $templateName = $this->detailsVerificationTemplateName;
            $mediaUrl = $this->detailsVerificationMediaUrl;
        } else {
            return ['status' => false, 'message' => 'Invalid document template type',];
        }

        if (!$this->baseUrl || !$apiKey || !$templateName || !$mediaUrl) {
            Log::error('INTERAKT DOCUMENT CONFIG MISSING', ['template_type' => $templateType,]);
            return ['status' => false, 'message' => 'Interakt config missing',];
        }

        $payload = [
            "fullPhoneNumber" => '+91' . $mobile,
            "callbackData" => "application-document",
            "type" => "Template",
            "template" => [
                "name" => $templateName,
                "languageCode" => "en",
                "headerValues" => [
                    $mediaUrl
                ],
                "bodyValues" => [
                    $name
                ]
            ]
        ];

        if ($templateType === 'appointment') {
            $payload['template']['buttonValues'] = json_decode(
                '{"0":' . json_encode([$documentUrl]) . '}'
            );
        } else {
            $payload['template']['bodyValues'][] = $documentUrl;
        }

        // $payload['template']['buttonValues'] = json_decode(
        //     '{"0":' . json_encode([$documentUrl]) . '}'
        // );

        try {

            $http = app()->environment('local')
                ? Http::withoutVerifying()
                : Http::acceptJson();

            $response = $http
                ->withHeaders([
                    'Authorization' => 'Basic ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post(
                    $this->baseUrl . '/v1/public/message/',
                    $payload
                );

            if (!$response->successful()) {
                Log::error('INTERAKT DOCUMENT SEND FAILED', ['template_type' => $templateType, 'status_code' => $response->status(), 'response' => $response->body(),]);
            }

            return [
                'status' => $response->successful(),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {

            Log::error('INTERAKT SEND ERROR : ' . $e->getMessage());

            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
