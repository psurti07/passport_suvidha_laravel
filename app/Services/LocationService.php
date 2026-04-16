<?php
namespace App\Services;

class LocationService
{
    public static function getByPincode($pincode)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://geoloc.in/api/pincode',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['pincode' => $pincode]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . env('GEOLOC_KEY')
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            curl_close($curl);
            return ['error' => 'Unable to fetch data'];
        }

        curl_close($curl);

        $data = json_decode($response);

        if ($data && isset($data->status) && $data->status === "success") {
            return [
                'city' => $data->data[0]->cityname ?? '',
                'state' => $data->data[0]->statename ?? ''
            ];
        }

        return ['error' => 'Invalid Pincode'];
    }
}