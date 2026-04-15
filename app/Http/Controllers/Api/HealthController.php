<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthController extends Controller
{
    /**
     * Check the health of the API
     *
     * @return JsonResponse
     */
    public function check(): JsonResponse
    {
        $status = 'ok';
        $components = [
            'database' => true,
            'redis' => true,
            'storage' => true,
        ];
        $message = 'System is healthy';

        try {
            // Check database connection
            DB::connection()->select('SELECT 1');
        } catch (\Exception $e) {
            $status = 'error';
            $components['database'] = false;
            $message = 'Database connection failed';
        }

        // Check Redis if configured
        if (config('cache.default') === 'redis') {
            try {
                $redis = Redis::connection();
                $redis->ping();
            } catch (\Exception $e) {
                $status = 'error';
                $components['redis'] = false;
                $message = 'Redis connection failed';
            }
        }

        // Check storage directory is writable
        if (!is_writable(storage_path('app'))) {
            $status = 'error';
            $components['storage'] = false;
            $message = 'Storage directory is not writable';
        }

        // Get app version
        $version = config('app.version', '1.0.0');

        return response()->json([
            'status' => $status,
            'message' => $message,
            'version' => $version,
            'timestamp' => now()->toIso8601String(),
            'components' => $components,
        ]);
    }
} 