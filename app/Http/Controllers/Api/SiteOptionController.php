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
}