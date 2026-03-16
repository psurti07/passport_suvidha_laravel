<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Customer;
use App\Models\ApplicationOrder;
use App\Models\Invoice;
use App\Models\InvoiceLog;

use Illuminate\Validation\Rule;

use App\Exports\CustomersExport;      
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Maatwebsite\Excel\Excel as ExcelConstant; 
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Carbon; 
use Illuminate\Support\Str;

use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index(Request $request)
    // {
    //     $query = Customer::query();

    //     // Search functionality
    //     if ($request->filled('search')) {
    //         $searchTerm = $request->search;
    //         $query->where(function($q) use ($searchTerm) {
    //             $q->where('pack_code', 'like', "%{$searchTerm}%")
    //               ->orWhere('first_name', 'like', "%{$searchTerm}%")
    //               ->orWhere('last_name', 'like', "%{$searchTerm}%")
    //               ->orWhere('email', 'like', "%{$searchTerm}%")
    //               ->orWhere('mobile_number', 'like', "%{$searchTerm}%")
    //               ->orWhere('service_code', 'like', "%{$searchTerm}%");
    //         });
    //     }

    //     // Date range filtering
    //     if ($request->filled('from_date')) {
    //         $query->whereDate('created_at', '>=', $request->from_date);
    //     }
    //     if ($request->filled('to_date')) {
    //         $query->whereDate('created_at', '<=', $request->to_date);
    //     }

    //     // Filter by is_paid status
    //     if ($request->filled('status')) { // Use filled() for cleaner check
    //         $status = $request->input('status');
    //         if ($status === 'paid') {
    //             $query->where('is_paid', true);
    //         } elseif ($status === 'lead') {
    //             $query->where('is_paid', false);
    //         }
    //          // No 'else' needed, as filled() handles the 'All' case (empty value)
    //     }

    //     // Sorting logic
    //     $sortBy = $request->input('sort_by', 'id'); // Default sort column
    //     $sortDirection = $request->input('sort_direction', 'desc'); // Default sort direction

    //     // Validate sortable columns to prevent errors
    //     $sortableColumns = ['id', 'first_name', 'email', 'mobile_number', 'is_paid', 'created_at'];
    //     if (in_array($sortBy, $sortableColumns)) {
    //          // Combine first_name and last_name sorting if 'first_name' is chosen
    //          if ($sortBy === 'first_name') {
    //              $query->orderBy('first_name', $sortDirection)
    //                    ->orderBy('last_name', $sortDirection); // Secondary sort by last name
    //          } else {
    //             $query->orderBy($sortBy, $sortDirection);
    //          }
    //     } else {
    //         // Default sort if invalid column provided
    //         $query->orderBy('id', 'desc');
    //     }

    //     // Apply pagination and append query string parameters
    //     $perPage = $request->input('per_page', 10); // Get per_page value from request
    //     $paginator = $query->paginate($perPage);
    //     $customers = $paginator->withQueryString(); // Chain withQueryString directly

    //     return view('admin.customers.index-old', compact('customers'));
    // }

    public function index()
    {
        return view('admin.customers.index');
    }

    public function data(Request $request)
    {
        $query = Customer::select([
            'id',
            'first_name',
            'last_name',
            'email',
            'mobile_number',
            'is_paid',
            'created_at'
        ]);

        // Date Filter
        if ($request->from_date && $request->to_date) {

            $query->whereBetween('created_at', [
                $request->from_date,
                $request->to_date
            ]);
        }

        // Status Filter
        if ($request->status == "paid") {
            $query->where('is_paid', 1);
        }

        if ($request->status == "lead") {
            $query->where('is_paid', 0);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('name', function ($row) {
                return $row->first_name . ' ' . $row->last_name;
            })

            ->addColumn('status', function ($row) {
                return $row->is_paid ? 'paid' : 'lead';
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y H:i:s');
            })

            ->rawColumns(['status'])

            ->make(true);
    }

    /**
     * Display a listing of today's customers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function today(Request $request)
    {
        // Base query for today's customers
        $baseQuery = Customer::query()->whereDate('created_at', Carbon::today());

        // Calculate counts before filtering for display
        $totalTodayCount = $baseQuery->count();
        $paidTodayCount = (clone $baseQuery)->where('is_paid', true)->count(); // Clone to avoid modifying base query
        $leadTodayCount = (clone $baseQuery)->where('is_paid', false)->count(); // Clone to avoid modifying base query

        // Apply filters to a new query instance for pagination
        $filteredQuery = Customer::query()->whereDate('created_at', Carbon::today());

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $filteredQuery->where(function($q) use ($searchTerm) {
                $q->where('pack_code', 'like', "%{$searchTerm}%")
                  ->orWhere('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('mobile_number', 'like', "%{$searchTerm}%")
                  ->orWhere('service_code', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by is_paid status
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'paid') {
                $filteredQuery->where('is_paid', true);
            } elseif ($status === 'lead') {
                $filteredQuery->where('is_paid', false);
            }
        }

        // Sorting logic
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');

        $sortableColumns = ['id', 'first_name', 'email', 'mobile_number', 'is_paid', 'created_at'];
        if (in_array($sortBy, $sortableColumns)) {
             if ($sortBy === 'first_name') {
                 $filteredQuery->orderBy('first_name', $sortDirection)
                       ->orderBy('last_name', $sortDirection);
             } else {
                $filteredQuery->orderBy($sortBy, $sortDirection);
             }
        } else {
            $filteredQuery->orderBy('id', 'desc');
        }

        // Apply pagination to the filtered query
        $perPage = $request->input('per_page', 10);
        $customers = $filteredQuery->paginate($perPage)->withQueryString();

        // Return the view with counts and paginated customers
        return view('admin.customers.today', compact('customers', 'totalTodayCount', 'paidTodayCount', 'leadTodayCount'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $baseRules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => 'required','regex:/^[6-9][0-9]{9}$/',
            'email' => 'required|email|unique:customers,email',
            'is_paid' => 'sometimes|boolean',
        ];

        $paidRules = [
            'address' => 'required|string',
            'pin_code' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'service_code' => 'required|string|max:255',
            'card_number' => 'nullable|string',
            'amount' => 'nullable|numeric',
            'paymentid' => 'nullable|string'
        ];

        $isPaid = $request->boolean('is_paid');

        $rules = $baseRules;

        if ($isPaid) {
            $rules = array_merge($rules, $paidRules);
        }

        $validatedData = $request->validate($rules);

        $validatedData['is_paid'] = $isPaid;
        $validatedData['registration_step'] = $isPaid ? 4 : 1;

        if ($isPaid && isset($validatedData['service_code'])) {

            switch ($validatedData['service_code']) {

                case 'NORMAL_36':
                    $validatedData['passport_type'] = 'normal';
                    $validatedData['book_size'] = 36;
                    break;

                case 'NORMAL_60':
                    $validatedData['passport_type'] = 'normal';
                    $validatedData['book_size'] = 60;
                    break;

                case 'TATKAL_36':
                    $validatedData['passport_type'] = 'tatkal';
                    $validatedData['book_size'] = 36;
                    break;

                case 'TATKAL_60':
                    $validatedData['passport_type'] = 'tatkal';
                    $validatedData['book_size'] = 60;
                    break;
            }
        }
        
        DB::transaction(function () use ($validatedData, $isPaid, $request) {
            
            $customer = Customer::create($validatedData);

            if ($isPaid) {

                $regDate = Carbon::now()->format('Y-m-d');

                $cardNumber = $request->card_number ?? rand(1000000000000000,9999999999999999);

                $paymentId = $request->paymentid ?? 'cash_' . Str::random(13);

                $netAmount = $request->amount ?? 0;

                $cgstAmount = 0;
                $sgstAmount = 0;
                $igstAmount = 0;

                if ($request->state == 'Gujarat') {
                    $cgstAmount = $netAmount * 0.09;
                    $sgstAmount = $netAmount * 0.09;

                } else {
                    $igstAmount = $netAmount * 0.18;
                }

                $grandTotal = $netAmount + $cgstAmount + $sgstAmount + $igstAmount;

                $order = ApplicationOrder::create([
                    'customer_id' => $customer->id,
                    'registration_date' => $regDate,
                    'expiry_date' => Carbon::parse($regDate)->addMonths(6),
                    'card_number' => $cardNumber,
                    'amount' => $grandTotal,
                    'paymentid' => $paymentId
                ]);

                $invoiceno = Invoice::max('inv_no') + 1;

                $invoice = Invoice::create([
                    'customer_id' => $customer->id,
                    'card_id' => $order->id,
                    'inv_date' => Carbon::now()->format('Y-m-d'),
                    'inv_no' => $invoiceno,
                    'net_amount' => $netAmount,
                    'cgst' => $cgstAmount,
                    'sgst' => $sgstAmount,
                    'igst' => $igstAmount,
                    'total_amount' => $grandTotal
                ]);

                InvoiceLog::create([
                    'log_detail' => 'Create New Customer',
                    'card_number' => $order->id,
                    'invoice_id' => $invoice->id,
                    'staff_id' => Auth::id()
                ]);
            }
        });
        
        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        // Prevent accessing details page for leads
        if (!$customer->is_paid) {
            return redirect()->route('admin.customers.index')
                             ->with('error', 'Cannot view details for a Lead customer.');
        }

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        // Validation rules similar to API controller update
         $baseRules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'email' => ['required', 'email', Rule::unique('customers')->ignore($customer->id)],
            'is_paid' => 'sometimes|boolean',
        ];
        $paidRules = [
            'pack_code' => 'required|string|max:255',
            'address' => 'required|string',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'payment_info_id' => 'required|numeric',
            'service_code' => 'required|string|max:255',
        ];

        // Use the is_paid value submitted in the form, default to current status if not submitted
        $isPaid = $request->boolean('is_paid', $customer->is_paid); 

        $rules = $baseRules;
        if ($isPaid) {
            $rules = array_merge($rules, $paidRules);
        }

        $validatedData = $request->validate($rules);
        $validatedData['is_paid'] = $isPaid; // Ensure is_paid is set correctly

         // Fill missing nullable fields with null if not paid
        if (!$isPaid) {
             $nullableFields = ['pack_code', 'address', 'gender', 'date_of_birth', 'place_of_birth', 'nationality', 'payment_info_id', 'service_code'];
             foreach ($nullableFields as $field) {
                 // Only nullify if not present in the request data, otherwise keep submitted value (even if empty string)
                 if (!array_key_exists($field, $validatedData)) {
                     $validatedData[$field] = null;
                 }
             }
        }

        $customer->update($validatedData);

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully');
    }

    public function export(Request $request)
    {
        // Query Building (Replicate index logic)
        $query = Customer::query();

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('pack_code', 'like', "%{$searchTerm}%")
                  ->orWhere('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('mobile_number', 'like', "%{$searchTerm}%")
                  ->orWhere('service_code', 'like', "%{$searchTerm}%");
            });
        }

        // Date range filtering
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Filter by is_paid status
        if ($request->filled('status')) { 
            $status = $request->input('status');
            if ($status === 'paid') {
                $query->where('is_paid', true);
            } elseif ($status === 'lead') {
                $query->where('is_paid', false);
            }
        }

        // Sorting logic
        $sortBy = $request->input('sort_by', 'id'); 
        $sortDirection = $request->input('sort_direction', 'desc');

        $sortableColumns = ['id', 'first_name', 'email', 'mobile_number', 'is_paid', 'created_at'];
        if (in_array($sortBy, $sortableColumns)) {
             if ($sortBy === 'first_name') {
                 $query->orderBy('first_name', $sortDirection)
                       ->orderBy('last_name', $sortDirection);
             } else {
                $query->orderBy($sortBy, $sortDirection);
             }
        } else {
            $query->orderBy('id', 'desc');
        }

        $customers = $query->get(); // Fetch all matching customers for export

        // Handle export based on type
        $type = $request->input('type', 'excel');
        // Use correct file extensions
        $filename = 'customers.' . ($type === 'excel' ? 'xlsx' : $type);

        switch($type) {
            case 'excel':
                // Provide filename with correct extension and explicit type
                return ExcelFacade::download(new CustomersExport($customers), $filename, ExcelConstant::XLSX);
            case 'csv':
                // Filename already has .csv, explicit type is good practice
                return ExcelFacade::download(new CustomersExport($customers), $filename, ExcelConstant::CSV);
            case 'pdf':
                // Filename needs .pdf extension here too
                $pdfFilename = 'customers.pdf';
                try {
                    return Pdf::loadView('exports.customers', ['customers' => $customers])
                             ->download($pdfFilename);
                } catch (\Exception $e) {
                    Log::error("PDF Export Error: " . $e->getMessage());
                    return redirect()->back()->with('error', 'Could not generate PDF export.');
                }
            default:
                return redirect()->back()->with('error', 'Invalid export type requested.');
        }
    }
}
