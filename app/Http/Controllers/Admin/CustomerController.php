<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Customer;
use App\Models\ApplicationOrder;
use App\Models\Invoice;
use App\Models\InvoiceLog;

use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Carbon; 
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Hash;

use Yajra\DataTables\Facades\DataTables;

use App\Services\LocationService;

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
            'cardNumber' => Str::upper(Str::random(16)),
            'paymentId'   => 'cash_' . Str::upper(Str::random(10)),
        ]);
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
            'card_number' => 'nullable|string|size:16',
            'amount' => 'nullable|numeric|min:1',
            'payment_id' => 'nullable|string|max:50',
        ];

        $isPaid = $request->boolean('is_paid');

        $rules = $isPaid ? array_merge($baseRules, $paidRules) : $baseRules;

        $validated = $request->validate($rules);

        $validated['is_paid'] = $isPaid;

        $this->createOrConvert($validated, null, 'create');

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
            'card_number' => 'nullable|string|size:16',
            'amount' => 'nullable|numeric|min:1',
            'payment_id' => 'nullable|string|max:50',
        ]);

        $validated['is_paid'] = true;

        $this->createOrConvert($validated, $customer, 'convert');

        return redirect()
            ->route('admin.customers.show', $customer->id)
            ->with('success', 'Lead converted successfully')
            ->withFragment('info');
    }

    private function createOrConvert($validated, $customer = null, $type = 'create')
    {
        return DB::transaction(function () use ($validated, $customer, $type) {

            $isPaid = $validated['is_paid'];

            $customerData = collect($validated)->except([
                'amount',
                'card_number',
                'payment_id'
            ])->toArray();

            $customerData['registration_step'] = $isPaid ? 4 : 1;

            if ($isPaid) {
                $customerData['password'] = Hash::make(Str::random(8));
            }

            $services = [
                'NORMAL_36' => ['type' => 'normal', 'size' => 36],
                'NORMAL_60' => ['type' => 'normal', 'size' => 60],
                'TATKAL_36' => ['type' => 'tatkal', 'size' => 36],
                'TATKAL_60' => ['type' => 'tatkal', 'size' => 60],
            ];

            if ($isPaid && isset($services[$validated['service_code']])) {
                $customerData['passport_type'] = $services[$validated['service_code']]['type'];
                $customerData['book_size'] = $services[$validated['service_code']]['size'];
            }

            if ($customer) {
                $customer->update($customerData);
            } else {
                $customer = Customer::create($customerData);
            }

            if (!$isPaid) {
                return $customer;
            }

            $netAmount = $validated['amount'] ?? 0;

            $cardNumber = $validated['card_number'] ?? Str::upper(Str::random(16));
            $paymentId  = $validated['payment_id'] ?? 'cash_' . Str::upper(Str::random(10));

            $cgst = $sgst = $igst = 0;

            if (strtolower($validated['state']) === 'gujarat') {
                $cgst = round($netAmount * 0.09, 2);
                $sgst = round($netAmount * 0.09, 2);
            } else {
                $igst = round($netAmount * 0.18, 2);
            }

            $totalAmount = $netAmount + $cgst + $sgst + $igst;

            $order = ApplicationOrder::create([
                'customer_id' => $customer->id,
                'registration_date' => now()->toDateString(),
                'expiry_date' => now()->addMonths(6),
                'card_number' => $cardNumber,
                'amount' => $totalAmount,
                'payment_id' => $paymentId
            ]);

            $invoiceNo = (DB::table('invoices')->max('inv_no') ?? 0) + 1;

            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'card_id' => $order->id,
                'inv_date' => now(),
                'inv_no' => $invoiceNo,
                'net_amount' => $netAmount,
                'cgst' => $cgst,
                'sgst' => $sgst,
                'igst' => $igst,
                'total_amount' => $totalAmount
            ]);

            InvoiceLog::create([
                'log_detail' => $type === 'convert'
                    ? 'Convert Lead to Customer'
                    : 'Create New Customer',
                'card_number' => $order->id,
                'invoice_id' => $invoice->id,
                'staff_id' => auth()->id()
            ]);

            return $customer;
        });
    }

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