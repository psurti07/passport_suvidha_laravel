<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinalDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FinalDetailController extends Controller
{
    /**
     * Get application summary details for the authenticated customer
     */
    public function getApplicationSummary()
    {
        $customer = Auth::guard('customer')->user();
        $finalDetail = FinalDetail::where('customer_id', $customer->id)
            ->latest()
            ->first();

        if (!$finalDetail) {
            return response()->json([
                'message' => 'No application summary found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Application summary retrieved successfully',
            'data' => [
                'file_name' => 'Application_Summary.pdf',
                'upload_date' => $finalDetail->upload_date,
                'is_approved' => $finalDetail->is_approved,
                'approved_date' => $finalDetail->approved_date,
                'customer_details' => [
                    'name' => $customer->full_name,
                    'date_of_birth' => $customer->date_of_birth,
                    'address' => $customer->address,
                    'city' => $customer->city,
                    'state' => $customer->state,
                    'country' => $customer->country,
                    'postal_code' => $customer->postal_code,
                ]
            ]
        ]);
    }

    /**
     * Preview the application summary PDF
     */
    public function preview()
    {
        $customer = Auth::guard('customer')->user();
        $finalDetail = FinalDetail::where('customer_id', $customer->id)
            ->latest()
            ->first();
        
        if (!$finalDetail) {
            return response()->json(['message' => 'No application summary found'], 404);
        }

        $filePath = storage_path('app/public/' . $finalDetail->file_path);
        
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->file($filePath);
    }

    /**
     * Download the application summary PDF
     */
    public function download()
    {
        $customer = Auth::guard('customer')->user();
        $finalDetail = FinalDetail::where('customer_id', $customer->id)
            ->latest()
            ->first();
        
        if (!$finalDetail) {
            return response()->json(['message' => 'No application summary found'], 404);
        }

        $filePath = storage_path('app/public/' . $finalDetail->file_path);
        
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->download($filePath, 'Application_Summary.pdf');
    }

    /**
     * Verify application summary
     */
    public function verifyApplication(Request $request)
    {
        $request->validate([
            'is_verified' => 'required|boolean'
        ]);

        $customer = Auth::guard('customer')->user();
        $finalDetail = FinalDetail::where('customer_id', $customer->id)
            ->latest()
            ->first();

        if (!$finalDetail) {
            return response()->json(['message' => 'No application summary found'], 404);
        }

        if ($request->is_verified) {
            $finalDetail->update([
                'is_approved' => true,
                'approved_date' => now(),
                'approved_by' => $customer->id,
                'approved_by_role' => 'customer'
            ]);

            return response()->json([
                'message' => 'Application verified successfully',
                'data' => [
                    'is_approved' => true,
                    'approved_date' => $finalDetail->approved_date
                ]
            ]);
        }

        return response()->json([
            'message' => 'Application verification required',
            'data' => [
                'is_approved' => false
            ]
        ], 422);
    }
} 