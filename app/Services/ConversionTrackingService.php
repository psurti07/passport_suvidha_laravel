<?php

namespace App\Services;

use App\Http\Middleware\Authenticate;
use App\Models\Customer;
use App\Models\FbAdsEntry;
use App\Models\SiteOption;
use Exception;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConversionTrackingService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config("services.interkt.url");
        $this->apiKey = config("services.interkt.key");
    }

    public function userTrack(array $data)
    {
        try {

            $response = Http::withHeaders([
                'Authorization' => "Basic " . $this->apiKey,
                'Accept' => "application/json"
            ])->post($this->baseUrl . '/track/users/', $data);

            if (!$response->successful()) {
                Log::error('User Track Response error: ', [
                    'status' => $response->status(),
                    'message' => 'User Track API Error: ' . $response->body(),
                ]);
            }

            return [
                'status' => $response->status(),
                'body' => $response->body(),
                'json' => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error('Interakt User Track Error', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function eventTrack(array $data = [])
    {
        try {

            $response = Http::withHeaders([
                'Authorization' => "Basic " . $this->apiKey,
                'Accept' => 'application/json'
            ])->post($this->baseUrl . '/track/events/',  $data);

            if (!$response->successful()) {

                Log::error('Event Track Response error:', [
                    'status' => $response->status(),
                    'message' => 'User Track API Error: ' . $response->body(),
                ]);
            }

            return [
                'status' => $response->status(),
                'body' => $response->body(),
                'json' => $response->json()
            ];
        } catch (\Exception $e) {

            Log::error('Event Track Response error:', [
                'status' => false,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
