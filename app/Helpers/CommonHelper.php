<?php

if (!function_exists('generateCardNumber')) {
    function generateCardNumber()
    {
        return \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(16));
    }
}

if (!function_exists('generatePaymentId')) {
    function generatePaymentId()
    {
        return 'cash_' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(10));
    }
}