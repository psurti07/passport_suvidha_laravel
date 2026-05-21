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
