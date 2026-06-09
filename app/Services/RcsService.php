<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SiteOption;

class RcsService
{
    protected $baseUrl;
    protected $userId;
    protected $apiKey;
    protected $templateId;

    public function __construct()
    {
        $this->baseUrl = 'https://rcsapi.rcscloud.smartping.io';
        $this->userId = SiteOption::getValue('rcs-user-id');
        $this->apiKey = SiteOption::getValue('rcs-api-key');
        $this->templateId = SiteOption::getValue('rcs-template-id');
    }

    // Generate API Token
    public function getToken()
    {
        try {

            $http = app()->environment('local')
                ? Http::withoutVerifying()
                : Http::acceptJson();

            $response = $http->post(
                $this->baseUrl . '/rcs/api/user/authorize',
                [
                    'userId' => $this->userId,
                    'apiKey' => $this->apiKey,
                ]
            );

            $data = $response->json();

            return $data['data']['apiToken'] ?? null;
        } catch (\Exception $e) {

            Log::error('RCS TOKEN ERROR : ' . $e->getMessage());

            return null;
        }
    }

    // Send RCS Messages
    public function send(array $mobiles)
    {
        $token = $this->getToken();

        if (!$token) {

            return [
                'status' => false,
                'message' => 'Unable to generate token',
            ];
        }

        $messages = [];

        foreach ($mobiles as $mobile) {

            $messages[] = [
                "templateId" => $this->templateId,
                "to" => $mobile,
                "customOne" => "1",
                "customTwo" => "1",
                "customThree" => "1",
                "customFour" => "1",
                "components" => [
                    "richCard" => [
                        [
                            "type" => "messageText",
                            "parameters" => []
                        ],
                        [
                            "type" => "messageDescription",
                            "parameters" => []
                        ],
                        [
                            "type" => "dynamicSuggestionURL",
                            "parameters" => []
                        ]
                    ]
                ]
            ];
        }

        try {

            $http = app()->environment('local')
                ? Http::withoutVerifying()
                : Http::acceptJson();

            $response = $http
                ->withHeaders([
                    'Authorization' => $token,
                    'Content-Type' => 'application/json',
                ])
                ->post(
                    $this->baseUrl . '/rcs/api/message/send',
                    [
                        'messages' => $messages
                    ]
                );

            return [
                'status' => true,
                'response' => $response->body(),
            ];
        } catch (\Exception $e) {

            Log::error('RCS SEND ERROR : ' . $e->getMessage());

            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
