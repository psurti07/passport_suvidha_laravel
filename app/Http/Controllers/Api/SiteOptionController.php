<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteOption;

class SiteOptionController extends Controller
{
    public function getWelcomeMessage()
    {
        $option = SiteOption::where('option_key', 'welcome-message')->first();

        return response()->json([
            'message' => $option->option_value ?? null,
        ]);
    }

    public function getCustomerMessage()
    {
        $option = SiteOption::where('option_key', 'customer-message')->first();

        return response()->json([
            'message' => $option->option_value ?? null,
        ]);
    }

    public function getFbPixel()
    {
        try {
            $options = SiteOption::whereIn('option_key', [
                'facebook-domain-verification-id',
                'facebook-pixel-key'
            ])->pluck('option_value', 'option_key');

            return response()->json([
                'success' => true,
                'fb_pixel_key' => $options['facebook-pixel-key'] ?? null,
                'domain_verification' => $options['facebook-domain-verification-id'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching site options.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
