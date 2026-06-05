<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\FbAdsEntry;
use App\Models\SiteOption;
use Illuminate\Support\Facades\Http;

class FacebookConversionService
{
    public function send(Customer $customer)
    {
        $fbLead = FbAdsEntry::where(
            'customer_id',
            $customer->id
        )->first();

        if (!$fbLead) {
            return false;
        }

        $pixelId = SiteOption::getValue('facebook-pixel-key');

        $accessToken = SiteOption::getValue(
            'facebook-access-token'
        );

        $eventName = SiteOption::getValue(
            'facebook-event-name'
        );

        $eventId = SiteOption::getValue(
            'facebook-event-id'
        );

        $payload = [
            'data' => [
                [
                    'event_name' => $eventName,
                    'event_time' => time(),
                    'event_id' => $eventId,
                    'event_source_url' => config('app.url'),
                    'action_source' => 'website',

                    'user_data' => [
                        'fn' => [
                            hash(
                                'sha256',
                                strtolower($customer->first_name)
                            )
                        ],

                        'em' => [
                            hash(
                                'sha256',
                                strtolower($customer->email)
                            )
                        ],

                        'ph' => [
                            hash(
                                'sha256',
                                $customer->mobile_number
                            )
                        ],

                        'ct' => [
                            hash(
                                'sha256',
                                strtolower($customer->city ?? '')
                            )
                        ],

                        'st' => [
                            hash(
                                'sha256',
                                strtolower($customer->state ?? '')
                            )
                        ],

                        'country' => [
                            hash('sha256', 'in')
                        ],

                        'client_ip_address' => request()->ip(),

                        'client_user_agent' =>
                        request()->userAgent(),

                        'fbc' => $fbLead->fbclid
                    ],

                    'custom_data' => [
                        'currency' => 'INR',
                        'value' => 499,
                        'status' => 'registered'
                    ]
                ]
            ]
        ];

        if (!empty($fbLead->fbclid)) {
            $payload['data'][0]['user_data']['fbc']
                = $fbLead->fbclid;
        }

        $http = app()->environment('local')
            ? Http::withoutVerifying()
            : Http::acceptJson();

        $response = $http->post(
            "https://graph.facebook.com/v16.0/{$pixelId}/events",
            [
                'access_token' => $accessToken,
                'data' => $payload['data']
            ]
        );

        $fbLead->update([
            'sent_data' => $payload,
            'received_data' => $response->json()
        ]);

        return true;
    }
}
