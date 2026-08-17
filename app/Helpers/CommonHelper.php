<?php

if (!function_exists('generateCardNumber')) {
    function generateCardNumber()
    {
        $number = '';

        for ($i = 0; $i < 15; $i++) {
            $number .= random_int(0, 9);
        }

        $sum = 0;
        for ($i = 0; $i < 15; $i++) {
            $digit = (int) $number[$i];

            if ($i % 2 == 0) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;

        return $number . $checkDigit;
    }
}

if (!function_exists('generatePaymentId')) {
    function generatePaymentId()
    {
        return 'cash_' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(10));
    }
}

if (!function_exists('getOption')) {
    function getOption($key)
    {
        return \App\Models\SiteOption::where('option_key', $key)->value('option_value');
    }
}

if (!function_exists('encryptData')) {
    function encryptData($data)
    {
        $key = "jvJ7RGlyfjm0jwaa";
        $iv = "@@@@&&&&####$$$$";

        $encrypted = openssl_encrypt(
            $data,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return rtrim(strtr(base64_encode($iv . $encrypted), '+/', '-_'), '=');
    }
}

if (!function_exists('decryptData')) {
    function decryptData($data)
    {
        $key = "jvJ7RGlyfjm0jwaa";

        $data = strtr($data, '-_', '+/');

        $padding = strlen($data) % 4;

        if ($padding) {
            $data .= str_repeat('=', 4 - $padding);
        }

        $data = base64_decode($data);

        $iv = substr($data, 0, 16);

        $encrypted = substr($data, 16);

        return openssl_decrypt(
            $encrypted,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
    }
}

if (!function_exists('getStatusColor')) {
    function getStatusColor($slug)
    {
        return match (true) {

            in_array($slug, [
                'in_process',
            ]) => [
                'bg' => '#dbeafe',
                'text' => '#1e40af',
                'tailwind' => 'bg-blue-100 text-blue-800'
            ],

            in_array($slug, [
                'documents_submitted',
                'pov_success',
            ]) => [
                'bg' => '#dcfce7',
                'text' => '#166534',
                'tailwind' => 'bg-green-100 text-green-800'
            ],

            in_array($slug, [
                'details_verification',
                'not_contact_2_days_warning'
            ]) => [
                'bg' => '#fef9c3',
                'text' => '#854d0e',
                'tailwind' => 'bg-yellow-100 text-yellow-800'
            ],

            in_array($slug, [
                'appointment_scheduled',
                'appointment_rescheduled1',
                'appointment_rescheduled2',
                'appointment_rescheduled3'
            ])
            => [
                'bg' => '#ffedd5',
                'text' => '#9a3412',
                'tailwind' => 'bg-orange-100 text-orange-800'
            ],

            in_array($slug, [
                'pov_failed',
                'not_contact_2_days_reject'
            ])
            => [
                'bg' => '#fee2e2',
                'text' => '#991b1b',
                'tailwind' => 'bg-red-100 text-red-800'
            ],

            in_array($slug, [
                'pov_insufficient_documents'
            ])
            => [
                'bg' => '#f3f4f6',
                'text' => '#374151',
                'tailwind' => 'bg-gray-100 text-gray-800'
            ],

            in_array($slug, [
                'refunded'
            ])
            => [
                'bg' => '#f3e8ff',
                'text' => '#6b21a8',
                'tailwind' => 'bg-purple-100 text-purple-800'
            ],

            default => [
                'bg' => '#f3f4f6',
                'text' => '#374151',
                'tailwind' => 'bg-gray-100 text-gray-800'
            ],
        };
    }
}
