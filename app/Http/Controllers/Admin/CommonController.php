<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;    

use App\Services\LocationService;

class CommonController extends Controller
{
    public function getPincodeLocation(Request $request)
    {
        $request->validate([
            'pincode' => 'required|digits:6'
        ]);

        try {
            $result = LocationService::getByPincode($request->pincode);

            if (isset($result['error'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['error']
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'city' => $result['city'],
                'state' => $result['state']
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong'
            ], 500);
        }
    }
}
