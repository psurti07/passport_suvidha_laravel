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

use Illuminate\Support\Facades\Hash;

use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.customers.index');
    }

    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = Customer::select([
            'id',
            'first_name',
            'last_name',
            'email',
            'mobile_number',
            'is_paid',
            'created_at'
        ]);

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);
        }

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
    public function today()
    {
        $baseQuery = Customer::whereDate('created_at', Carbon::today());

        $totalTodayCount = $baseQuery->count();
        $paidTodayCount  = (clone $baseQuery)->where('is_paid', 1)->count();
        $leadTodayCount  = (clone $baseQuery)->where('is_paid', 0)->count();

        return view('admin.customers.today', compact(
            'totalTodayCount',
            'paidTodayCount',
            'leadTodayCount'
        ));
    }

    public function todayData(Request $request)
    {
        $query = Customer::select([
            'id',
            'first_name',
            'last_name',
            'email',
            'mobile_number',
            'is_paid',
            'created_at'
        ])->whereDate('created_at', now());

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.customers.create', [
            'card_number' => str_pad(rand(0, 9999999999999999), 16, '0', STR_PAD_LEFT),
            'payment_id'   => 'cash_' . Str::random(13),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     $baseRules = [
    //         'first_name' => 'required|string|max:255',
    //         'last_name' => 'required|string|max:255',
    //         'mobile_number' => ['required','regex:/^[6-9][0-9]{9}$/','unique:customers,mobile_number'],
    //         'email' => 'required|email|unique:customers,email',
    //         'is_paid' => 'sometimes|boolean',
    //     ];

    //     $paidRules = [
    //         'address' => 'required|string',
    //         'pin_code' => 'required|string|max:10',
    //         'city' => 'required|string|max:255',
    //         'state' => 'required|string|max:255',
    //         'gender' => 'required|in:male,female,other',
    //         'date_of_birth' => 'required|date',
    //         'place_of_birth' => 'required|string|max:255',
    //         'nationality' => 'required|string|max:255',
    //         'service_code' => 'required|string|max:50',
    //         'card_number' => 'nullable|digits:16',
    //         'amount' => 'nullable|numeric|min:1',
    //         'payment_id' => 'nullable|string|max:50'
    //     ];

    //     $isPaid = $request->boolean('is_paid');

    //     $rules = $isPaid
    //         ? array_merge($baseRules, $paidRules)
    //         : $baseRules;

    //     $validatedData = $request->validate($rules);

    //     $validatedData['is_paid'] = $isPaid;
    //     $validatedData['registration_step'] = $isPaid ? 4 : 1;

    //     if ($isPaid) {
    //         $password = Str::random(6);
    //         $validatedData['password'] = Hash::make($password);
    //     }

    //     $services = [
    //         'NORMAL_36' => ['type' => 'normal', 'size' => 36],
    //         'NORMAL_60' => ['type' => 'normal', 'size' => 60],
    //         'TATKAL_36' => ['type' => 'tatkal', 'size' => 36],
    //         'TATKAL_60' => ['type' => 'tatkal', 'size' => 60],
    //     ];

    //     if ($isPaid && isset($services[$validatedData['service_code']])) {
    //         $validatedData['passport_type'] = $services[$validatedData['service_code']]['type'];
    //         $validatedData['book_size'] = $services[$validatedData['service_code']]['size'];
    //     }

    //     DB::transaction(function () use ($validatedData, $request, $isPaid) {

    //         $customer = Customer::create($validatedData);

    //         if ($isPaid) {

    //             $regDate = now()->toDateString();

    //             $cardNumber = $request->card_number 
    //                 ?? substr(time() . rand(1000,9999), 0, 16);

    //             $paymentId = $request->payment_id 
    //                 ?? 'cash_' . Str::upper(Str::random(10));

    //             $netAmount = $request->amount ?? 0;

    //             $cgst = 0;
    //             $sgst = 0;
    //             $igst = 0;

    //             if ($netAmount > 0) {
    //                 if (strtolower($request->state) === 'gujarat') {
    //                     $cgst = round($netAmount * 0.09, 2);
    //                     $sgst = round($netAmount * 0.09, 2);
    //                 } else {
    //                     $igst = round($netAmount * 0.18, 2);
    //                 }
    //             }

    //             $totalAmount = $netAmount + $cgst + $sgst + $igst;

    //             $order = ApplicationOrder::create([
    //                 'customer_id' => $customer->id,
    //                 'registration_date' => $regDate,
    //                 'expiry_date' => now()->addMonths(6),
    //                 'card_number' => $cardNumber,
    //                 'amount' => $totalAmount,
    //                 'payment_id' => $paymentId
    //             ]);

    //             $invoiceNo = DB::table('invoices')
    //                 ->lockForUpdate()
    //                 ->max('inv_no') + 1;

    //             $invoice = Invoice::create([
    //                 'customer_id' => $customer->id,
    //                 'card_id' => $order->id,
    //                 'inv_date' => now(),
    //                 'inv_no' => $invoiceNo,
    //                 'net_amount' => $netAmount ?? 0,
    //                 'cgst' => $cgst,
    //                 'sgst' => $sgst,
    //                 'igst' => $igst,
    //                 'total_amount' => $totalAmount
    //             ]);

    //             InvoiceLog::create([
    //                 'log_detail' => 'Create New Customer',
    //                 'card_number' => $order->id,
    //                 'invoice_id' => $invoice->id,
    //                 'staff_id' => auth()->id()
    //             ]);
    //         }
    //     });

    //     return redirect()
    //         ->back()
    //         ->with('success', 'Customer created successfully');
    // }
    public function store(Request $request)
    {
        // ================= VALIDATION =================
        $baseRules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => ['required','regex:/^[6-9][0-9]{9}$/','unique:customers,mobile_number'],
            'email' => 'required|email|unique:customers,email',
            'is_paid' => 'sometimes|boolean',
        ];

        $paidRules = [
            'address' => 'required|string',
            'pin_code' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'service_code' => 'required|string|max:50',

            // OPTIONAL FIELDS ✅
            'card_number' => 'nullable|digits:16',
            'amount' => 'nullable|numeric|min:1',
            'payment_id' => 'nullable|string|max:50',
        ];

        $isPaid = $request->boolean('is_paid');

        $rules = $isPaid
            ? array_merge($baseRules, $paidRules)
            : $baseRules;

        $validated = $request->validate($rules);

        // ================= DEFAULT VALUES =================
        $validated['is_paid'] = $isPaid;
        $validated['registration_step'] = $isPaid ? 4 : 1;

        if ($isPaid) {
            $validated['password'] = Hash::make(Str::random(6));
        }

        // ================= SERVICE =================
        $services = [
            'NORMAL_36' => ['type' => 'normal', 'size' => 36],
            'NORMAL_60' => ['type' => 'normal', 'size' => 60],
            'TATKAL_36' => ['type' => 'tatkal', 'size' => 36],
            'TATKAL_60' => ['type' => 'tatkal', 'size' => 60],
        ];

        if ($isPaid && isset($services[$validated['service_code']])) {
            $validated['passport_type'] = $services[$validated['service_code']]['type'];
            $validated['book_size'] = $services[$validated['service_code']]['size'];
        }

        // ================= TRANSACTION =================
        DB::transaction(function () use ($validated, $request, $isPaid) {

            // ===== CREATE CUSTOMER =====
            $customer = Customer::create($validated);

            if (!$isPaid) return;

            // ================= SAFE VALUES =================
            $netAmount = $request->amount ?? 0;

            $cardNumber = $request->card_number 
                ?? substr(time() . rand(1000,9999), 0, 16);

            $paymentId = $request->payment_id 
                ?? 'cash_' . Str::upper(Str::random(10));

            // ================= GST =================
            $cgst = $sgst = $igst = 0;

            if ($netAmount > 0) {
                if (strtolower($request->state) === 'gujarat') {
                    $cgst = round($netAmount * 0.09, 2);
                    $sgst = round($netAmount * 0.09, 2);
                } else {
                    $igst = round($netAmount * 0.18, 2);
                }
            }

            $totalAmount = $netAmount + $cgst + $sgst + $igst;

            // ================= ORDER =================
            $order = ApplicationOrder::create([
                'customer_id' => $customer->id,
                'registration_date' => now()->toDateString(),
                'expiry_date' => now()->addMonths(6),
                'card_number' => $cardNumber,
                'amount' => $totalAmount,
                'payment_id' => $paymentId
            ]);

            // ================= INVOICE =================
            $invoiceNo = DB::table('invoices')
                ->lockForUpdate()
                ->max('inv_no') + 1;

            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'card_id' => $order->id,
                'inv_date' => now(),
                'inv_no' => $invoiceNo,
                'net_amount' => $netAmount, // 🔥 NEVER NULL
                'cgst' => $cgst,
                'sgst' => $sgst,
                'igst' => $igst,
                'total_amount' => $totalAmount
            ]);

            // ================= LOG =================
            InvoiceLog::create([
                'log_detail' => 'Create New Customer',
                'card_number' => $order->id,
                'invoice_id' => $invoice->id,
                'staff_id' => auth()->id()
            ]);
        });

        return back()->with('success', 'Customer created successfully');
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

        $customer->load('applicationDocuments.documentType');
        
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
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => [
                'required',
                'regex:/^[6-9][0-9]{9}$/',
                Rule::unique('customers')->ignore($customer->id)
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('customers')->ignore($customer->id)
            ],
            'address' => 'required|string',
            'pin_code' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
        ];

        $validatedData = $request->validate($rules);

        $customer->update($validatedData);

        return redirect()->back()->with('success', 'Customer updated successfully');       
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

    public function convertToCustomer(Request $request, Customer $customer)
    {
        if ($customer->is_paid) {
            return back()->with('error', 'Already converted');
        }

        $validated = $request->validate([
            'address' => 'required|string',
            'pin_code' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'service_code' => 'required|string|max:50',
            'card_number' => 'nullable|digits:16',
            'amount' => 'required|numeric|min:1',
            'payment_id' => 'required|string|max:50'
        ]);

        DB::transaction(function () use ($validated, $customer) {

            $services = [
                'NORMAL_36' => ['type' => 'normal', 'size' => 36],
                'NORMAL_60' => ['type' => 'normal', 'size' => 60],
                'TATKAL_36' => ['type' => 'tatkal', 'size' => 36],
                'TATKAL_60' => ['type' => 'tatkal', 'size' => 60],
            ];

            $netAmount = $validated['amount'];

            $cgst = $sgst = $igst = 0;

            if (strtolower($validated['state']) === 'gujarat') {
                $cgst = $netAmount * 0.09;
                $sgst = $netAmount * 0.09;
            } else {
                $igst = $netAmount * 0.18;
            }

            $total = $netAmount + $cgst + $sgst + $igst;

            $cardNumber = $validated['card_number']
                ?? substr(time() . rand(1000,9999), 0, 16);

            $paymentId = $validated['payment_id']
                ?? 'cash_' . Str::upper(Str::random(10));

            $update = $validated;

            $update['is_paid'] = 1;
            $update['registration_step'] = 4;

            if (isset($services[$validated['service_code']])) {
                $update['passport_type'] = $services[$validated['service_code']]['type'];
                $update['book_size'] = $services[$validated['service_code']]['size'];
            }

            $password = Str::random(6);
            $update['password'] = Hash::make($password);

            $customer->update($update);

            $order = ApplicationOrder::create([
                'customer_id' => $customer->id,
                'registration_date' => now()->toDateString(),
                'expiry_date' => now()->addMonths(6),
                'card_number' => $cardNumber,
                'amount' => $total,
                'payment_id' => $paymentId
            ]);

            $invoiceNo = DB::table('invoices')->lockForUpdate()->max('inv_no') + 1;

            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'card_id' => $order->id,
                'inv_date' => now(),
                'inv_no' => $invoiceNo,
                'net_amount' => $netAmount,
                'cgst' => $cgst,
                'sgst' => $sgst,
                'igst' => $igst,
                'total_amount' => $total
            ]);

            InvoiceLog::create([
                'log_detail' => 'Convert Lead to Customer',
                'card_number' => $order->id,
                'invoice_id' => $invoice->id,
                'staff_id' => auth()->id()
            ]);
        });

        return back()->with('success', 'Lead converted successfully');
    }

    // public function convertToCustomer(Request $request, Customer $customer)
    // {
    //     // =========================
    //     // Validation (FULL DATA)
    //     // =========================
    //     $rules = [
    //         'address' => 'required|string',
    //         'pin_code' => 'required|string|max:10',
    //         'city' => 'required|string|max:255',
    //         'state' => 'required|string|max:255',
    //         'gender' => 'required|in:male,female,other',
    //         'date_of_birth' => 'required|date',
    //         'place_of_birth' => 'required|string|max:255',
    //         'nationality' => 'required|string|max:255',
    //         'service_code' => 'required|string|max:50',
    //         'card_number' => 'nullable|digits:16',
    //         'amount' => 'required|numeric|min:1',
    //         'payment_id' => 'nullable|string|max:50'
    //     ];

    //     $validatedData = $request->validate($rules);

    //     // Already converted check
    //     if ($customer->is_paid) {
    //         return back()->with('error', 'Already converted');
    //     }

    //     DB::transaction(function () use ($request, $customer, $validatedData) {

    //         // =========================
    //         // Service Mapping
    //         // =========================
    //         $services = [
    //             'NORMAL_36' => ['type' => 'normal', 'size' => 36],
    //             'NORMAL_60' => ['type' => 'normal', 'size' => 60],
    //             'TATKAL_36' => ['type' => 'tatkal', 'size' => 36],
    //             'TATKAL_60' => ['type' => 'tatkal', 'size' => 60],
    //         ];

    //         // =========================
    //         // Amount Calculation
    //         // =========================
    //         $netamount = $validatedData['amount'];

    //         $cgst = $sgst = $igst = 0;

    //         if (strtolower($validatedData['state']) === 'gujarat') {
    //             $cgst = $netamount * 0.09;
    //             $sgst = $netamount * 0.09;
    //         } else {
    //             $igst = $netamount * 0.18;
    //         }

    //         $totalAmount = $netamount + $cgst + $sgst + $igst;

    //         // =========================
    //         // Card & Payment
    //         // =========================
    //         $cardNumber = $validatedData['card_number'] 
    //             ?? substr(time() . rand(1000,9999), 0, 16);

    //         $paymentId = $validatedData['payment_id'] 
    //             ?? 'cash_' . Str::upper(Str::random(10));

    //         // =========================
    //         // UPDATE CUSTOMER (MAIN PART 🔥)
    //         // =========================
    //         $updateData = $validatedData;

    //         $updateData['is_paid'] = 1;
    //         $updateData['registration_step'] = 4;

    //         // service_code → passport_type, book_size
    //         if (isset($services[$validatedData['service_code']])) {
    //             $updateData['passport_type'] = $services[$validatedData['service_code']]['type'];
    //             $updateData['book_size'] = $services[$validatedData['service_code']]['size'];
    //         }

    //         // optional password update
    //         $password = Str::random(6);
    //         $updateData['password'] = Hash::make($password);

    //         $customer->update($updateData);

    //         // =========================
    //         // CREATE ORDER
    //         // =========================
    //         $order = ApplicationOrder::create([
    //             'customer_id' => $customer->id,
    //             'registration_date' => now()->toDateString(),
    //             'expiry_date' => now()->addMonths(6),
    //             'card_number' => $cardNumber,
    //             'amount' => $totalAmount,
    //             'payment_id' => $paymentId
    //         ]);

    //         // =========================
    //         // INVOICE NUMBER
    //         // =========================
    //         $invoiceNo = DB::table('invoices')
    //             ->lockForUpdate()
    //             ->max('inv_no') + 1;

    //         // =========================
    //         // CREATE INVOICE
    //         // =========================
    //         $invoice = Invoice::create([
    //             'customer_id' => $customer->id,
    //             'card_id' => $order->id,
    //             'inv_date' => now(),
    //             'inv_no' => $invoiceNo,
    //             'net_amount' => $netamount,
    //             'cgst' => $cgst,
    //             'sgst' => $sgst,
    //             'igst' => $igst,
    //             'total_amount' => $totalAmount
    //         ]);

    //         // =========================
    //         // LOG
    //         // =========================
    //         InvoiceLog::create([
    //             'log_detail' => 'Convert Lead to Customer',
    //             'card_number' => $order->id,
    //             'invoice_id' => $invoice->id,
    //             'staff_id' => auth()->id()
    //         ]);
    //     });

    //     return back()->with('success', 'Lead converted to customer successfully');
    // }

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