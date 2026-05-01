<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\ApplicationOrder;
use App\Models\Invoice;
use App\Models\InvoiceLog;
use App\Models\ApplicationStatus;
use App\Models\Service;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Service::all();
        return view('admin.customers.index', compact('services'));
    }

    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = Customer::with('service')->select([
            'id',
            'service_id',
            'first_name',
            'last_name',
            'email',
            'mobile_number',
            'is_paid',
            'created_at'
        ])->where('is_paid', 1);

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);
        }

        if ($request->service) {
            $query->where('service_id', $request->service);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('service_name', function ($row) {
                if (!$row->service) {
                    return '-';
                }
                $isTatkal = str_starts_with($row->service->service_code, 'TP');
                return '<span>
                    ' . ($isTatkal ? '🟢 ' : '⚪') . $row->service->service_name . '
                </span>';
            })

            ->addColumn('customer_name', function ($row) {
                return $row->first_name . ' ' . $row->last_name;
            })

            ->editColumn('is_paid', function ($row) {
                if ($row->is_paid == '1') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">Paid</span>';
                }
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y, h:i A');
            })

            ->addColumn('actions', function ($row) {
                return '
                    <a href="' . route('admin.customers.show', $row->id) . '" 
                    class="text-blue-600 hover:text-blue-900" 
                    title="View">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd"
                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                ';
            })

            ->rawColumns(['service_name', 'is_paid', 'actions'])

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
        $services = Service::all();
        return view('admin.customers.today', compact('services'));
    }

    public function todayData(Request $request)
    {
        $query = Customer::select([
            'id',
            'service_id',
            'first_name',
            'last_name',
            'email',
            'mobile_number',
            'is_paid',
            'created_at'
        ])->whereDate('created_at', now())->where('is_paid', 1);

        if ($request->service) {
            $query->where('service_id', $request->service);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('service_name', function ($row) {
                if (!$row->service) {
                    return '-';
                }
                $isTatkal = str_starts_with($row->service->service_code, 'TP');
                return '<span>
                            ' . ($isTatkal ? '🟢 ' : '⚪') . $row->service->service_name . '
                        </span>';
            })

            ->addColumn('customer_name', function ($row) {
                return $row->first_name . ' ' . $row->last_name;
            })

            ->editColumn('is_paid', function ($row) {
                if ($row->is_paid == '1') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">Paid</span>';
                }
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y, h:i A');
            })

            ->addColumn('actions', function ($row) {
                return '
                    <a href="' . route('admin.customers.show', $row->id) . '" 
                    class="text-blue-600 hover:text-blue-900" 
                    title="View Customer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd"
                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                ';
            })

            ->rawColumns(['service_name', 'is_paid', 'actions'])

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
            'cardNumber' => generateCardNumber(),
            'paymentId'   => generatePaymentId(),
            'services' => Service::all()
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
            'mobile_number' => ['required', 'regex:/^[6-9][0-9]{9}$/', 'unique:customers,mobile_number'],
            'email' => 'required|email|unique:customers,email',
            'is_paid' => 'sometimes|boolean',
        ];

        $paidRules = [
            'service_id' => 'required|exists:services,id',
            'address' => 'required|string',
            'pin_code' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
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
        if (!$customer->is_paid) {
            return redirect()->back()
                ->with('error', 'Cannot view details for a Lead customer.');
        }

        $customer->load('applicationDocuments.documentType');

        $invoice = $customer->invoices()->latest()->first();

        $statuses = ApplicationStatus::orderBy('priority_no')->get();

        $predefinedMessages = \App\Models\PreDefinedMessage::select(
            'id',
            'message_name',
            'message_remarks',
            'status_id'
        )->get();

        return view('admin.customers.show', compact('customer', 'statuses', 'predefinedMessages', 'invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', [
            'cardNumber' => generateCardNumber(),
            'paymentId'   => generatePaymentId(),
            'customer' => $customer
        ]);
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

    /**
     * Handle both creating new customer and converting lead to customer
     */
    public function convertToCustomer(Request $request, Customer $customer)
    {
        if ($customer->is_paid) {
            return back()->with('error', 'Already converted');
        }

        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'address' => 'required|string',
            'pin_code' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'card_number' => 'nullable|string|size:16',
            'amount' => 'nullable|numeric|min:1',
            'payment_id' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.customer.search.form')
                ->withErrors($validator)
                ->withInput()
                ->with([
                    'mobileNo' => $customer->mobile_number,
                    'customer_id' => $customer->id
                ]);
        }

        $validated = $validator->validated();
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

            if (!$isPaid) {
                $customerData['service_id'] = 1;
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

            $cardNumber = $validated['card_number'] ?? generateCardNumber();
            $paymentId  = $validated['payment_id'] ?? generatePaymentId();

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
                'card_number' => $cardNumber,
                'amount' => $totalAmount,
                'payment_id' => $paymentId
            ]);

            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'service_id' => $customer->service_id,
                'order_id' => $order->id,
                'inv_date' => now(),
                'net_amount' => $netAmount,
                'cgst' => $cgst,
                'sgst' => $sgst,
                'igst' => $igst,
                'total_amount' => $totalAmount
            ]);

            $invoice->inv_no = 'INV_' . $invoice->id;
            $invoice->save();

            InvoiceLog::create([
                'invoice_id' => $invoice->id,
                'user_id' => auth()->id(),
                'log_detail' => $type === 'convert'
                    ? 'Convert Lead to Customer'
                    : 'Create New Customer',
                'card_number' => $order->id
            ]);

            return $customer;
        });
    }

    public function activate(Customer $customer)
    {
        $customer->update([
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.customers.show', $customer->id)
            ->with('success', 'Customer activated successfully')
            ->withFragment('actions');
    }

    public function deactivate(Customer $customer)
    {
        $customer->update([
            'is_active' => false,
        ]);

        return redirect()
            ->route('admin.customers.show', $customer->id)
            ->with('success', 'Customer deactivated successfully')
            ->withFragment('actions');
    }
}
