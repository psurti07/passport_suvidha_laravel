<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    public function passportServices()
    {
        $services = Service::all()->map(function ($service) {
            return [
                'id' => $service->id,
                'service_name' => $service->service_name,
                'service_code' => $service->service_code,
                'service_gov_amount' => (float) $service->service_gov_amount,
                'service_charges' => (float) $service->service_charges,
                'service_gst' => (float) $service->service_gst,
                'service_total_amount' => (float) $service->service_total_amount,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }
}
