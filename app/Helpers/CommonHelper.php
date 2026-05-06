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