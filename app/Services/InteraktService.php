<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SiteOption;

class InteraktService
{
    protected $baseUrl;
    protected $apiKey;
    protected $templateName;
    protected $mediaUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://api.interakt.ai';
        $this->apiKey = SiteOption::getValue('interakt-key');
        $this->templateName = SiteOption::getValue('interakt-template-name');
        $this->mediaUrl = SiteOption::getValue('interakt-media-url');
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
}
