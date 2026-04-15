<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\OtpsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class OtpController extends Controller
{
    public function index(Request $request)
    {
        $query = Otp::query();

        // Date filtering
        if ($request->filled('from_date')) {
            $query->whereDate('sent_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('sent_at', '<=', $request->to_date);
        }

        // Status filtering
        $status = $request->input('status');
        if ($status === 'verified') {
            $query->where('is_verified', true);
        } elseif ($status === 'pending') {
            $query->where('is_verified', false);
        }

        // Search filtering
        $search = $request->input('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                // Search in mobile_number or otp columns
                $q->where('mobile_number', 'like', "%{$search}%")
                  ->orWhere('otp', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'id'); // Default sort column
        $sortDirection = $request->input('sort_direction', 'desc'); // Default sort direction

        // Validate sortable columns
        $sortableColumns = ['id', 'mobile_number', 'sent_at', 'is_verified'];
        if (in_array($sortBy, $sortableColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            // Default sort if invalid column provided
            $query->orderBy('id', 'desc');
        }

        // Pagination
        $perPage = $request->input('per_page', 10); // Get per_page from request, default 10
        $otps = $query->paginate($perPage);

        return view('admin.otps.index', compact('otps'));
    }

    /**
     * Handle export requests for OTPs.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $type = $request->input('type', 'excel');
        $fileName = 'otps-' . date('Y-m-d-His');

        // Replicate the query logic from the index method (without pagination)
        $query = Otp::query();

        // Date filtering
        if ($request->filled('from_date')) {
            $query->whereDate('sent_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('sent_at', '<=', $request->to_date);
        }

        // Status filtering
        $status = $request->input('status');
        if ($status === 'verified') {
            $query->where('is_verified', true);
        } elseif ($status === 'pending') {
            $query->where('is_verified', false);
        }

        // Search filtering
        $search = $request->input('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('mobile_number', 'like', "%{$search}%")
                  ->orWhere('otp', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['id', 'mobile_number', 'sent_at', 'is_verified'];
        if (in_array($sortBy, $sortableColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('id', 'desc');
        }

        $otps = $query->get(); // Fetch all matching results

        // Use Maatwebsite/Excel for export
        if ($type === 'excel') {
            return Excel::download(new OtpsExport($otps), $fileName . '.xlsx');
        }
        if ($type === 'csv') {
             return Excel::download(new OtpsExport($otps), $fileName . '.csv');
        }

        // PDF Export
        if ($type === 'pdf') {
            $pdf = Pdf::loadView('admin.otps.pdf', compact('otps'));
            return $pdf->download($fileName . '.pdf');
        }

        // Fallback for unsupported types
        return response('Export type not supported.', 400);
    }
}
