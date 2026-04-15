<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GstRecord; // Assuming this is your GST data model
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Optional: If using raw DB queries or complex joins
// TODO: Add necessary use statements for export packages if you install them
use Maatwebsite\Excel\Facades\Excel; // Use Maatwebsite/Excel
use Barryvdh\DomPDF\Facade\Pdf; // Use laravel-dompdf
use App\Exports\GstReportExport; // Use the export class we created

class ReportController extends Controller
{
    /**
     * Display the GST report page with filtering, sorting, pagination, and export functionality.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View | mixed // Mixed for potential export responses
     */
    public function gstReport(Request $request)
    {
        // --- Validation (Optional but Recommended) ---
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'search' => 'nullable|string|max:255',
            'sort_by' => 'nullable|string|in:inv_date,inv_no,net_amount,cgst,sgst,igst,total_amount,fullname,mobile,email,gst_no,city,state', // Add valid sort columns
            'sort_direction' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
            'export' => 'nullable|string|in:excel,csv,pdf',
        ]);

        // --- Query Building ---
        $query = GstRecord::query(); // Start with your model

        // --- Filtering ---
        // Date Range Filter (using 'inv_date' column, adjust if different)
        if ($request->filled('from_date')) {
            $query->whereDate('inv_date', '>=', Carbon::parse($request->from_date));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('inv_date', '<=', Carbon::parse($request->to_date));
        }

        // Search Filter (across multiple columns)
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('inv_no', 'like', $searchTerm)
                  ->orWhere('fullname', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('mobile', 'like', $searchTerm)
                  ->orWhere('gst_no', 'like', $searchTerm)
                  ->orWhere('city', 'like', $searchTerm)
                  ->orWhere('state', 'like', $searchTerm);
                // Add other searchable columns if needed
            });
        }

        // --- Sorting ---
        $sortBy = $request->input('sort_by', 'inv_date'); // Default sort column
        $sortDirection = $request->input('sort_direction', 'desc'); // Default sort direction
        // Clone query before applying order for export to avoid ordering issues with pagination
        $exportQuery = clone $query;
        $exportQuery->orderBy($sortBy, $sortDirection);

        // --- Export Handling ---
        if ($request->filled('export')) {
            // Apply sorting specifically for export data if needed (already done above)
            $dataToExport = $exportQuery->get(); // Get all matching records for export
            $filename = 'gst_report_' . date('YmdHis');

            switch ($request->export) {
                case 'excel':
                    return Excel::download(new GstReportExport($dataToExport), $filename . '.xlsx');
                case 'csv':
                    return Excel::download(new GstReportExport($dataToExport), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV, [
                        'Content-Type' => 'text/csv', // Ensure correct headers for CSV
                    ]);
                case 'pdf':
                    $pdf = Pdf::loadView('exports.gst_report_pdf', ['data' => $dataToExport])
                              ->setPaper('a4', 'landscape'); // Optional: Set paper size and orientation
                    return $pdf->download($filename . '.pdf');
            }
        }

        // --- Pagination ---
        // Apply sorting to the main query for the view *before* pagination
        $query->orderBy($sortBy, $sortDirection);
        $perPage = $request->input('per_page', 10); // Default items per page
        $paginatedData = $query->paginate($perPage)->appends($request->query()); // Append query string to pagination links

        // --- Return View ---
        // Pass the paginated data with the key 'gstData' as expected by the view
        return view('admin.reports.gst_report', ['gstData' => $paginatedData]);
    }
} 