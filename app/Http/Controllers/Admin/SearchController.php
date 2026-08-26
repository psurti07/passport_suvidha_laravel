<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    // public function showSearchForm()
    // {
    //     $customer = null;
    //     $mobileNo = session('mobileNo');

    //     if (session('customer_id')) {
    //         $customer = Customer::find(session('customer_id'));
    //     }

    //     return view('admin.customer.search', [
    //         'customer' => $customer,
    //         'mobileNo' => $mobileNo,
    //         'cardNumber' => generateCardNumber(),
    //         'paymentId' => generatePaymentId(),
    //         'services' => Service::all()

    //     ]);
    // }

    public function showSearchForm()
    {
        $customer = null;

        $search = session('search');
        $serviceName = session('service_name');

        if (session('customer_id')) {
            $customer = Customer::with('service')->find(session('customer_id'));
        }

        return view('admin.customer.search', [
            'customer' => $customer,
            'search' => $search,
            'serviceName' => $serviceName,
            'cardNumber' => generateCardNumber(),
            'paymentId' => generatePaymentId(),
            'services' => Service::all(),
        ]);
    }

    public function searchCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => [
                'required',
                'string',
                'max:255',

                function ($attribute, $value, $fail) {

                    $value = trim($value);

                    if (ctype_digit($value)) {

                        if (!preg_match('/^[6-9][0-9]{9}$/', $value)) {
                            $fail('Please enter a valid 10-digit mobile number.');
                        }

                        return;
                    }

                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('Please enter a valid 10-digit mobile number or email address.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.customer.search.form')
                ->withErrors($validator)
                ->withInput();
        }

        $search = trim($request->search);

        $customer = null;

        if (ctype_digit($search)) {

            $customer = Customer::with('service')
                ->where('mobile_number', $search)
                ->first();
        } else {

            $customer = Customer::with('service')
                ->whereRaw('BINARY email = ?', [$search])
                ->first();
        }

        // Log::info('Customer search', [
        //     'search' => $search,
        //     'customer_id' => $customer?->id,
        //     'customer' => $customer,
        // ]);

        return redirect()
            ->route('admin.customer.search.form')
            ->with([
                'customer_id' => $customer?->id,
                'search' => $search,
                'service_name' => $customer?->service?->service_name,
            ]);
    }


    // public function searchCustomer(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'mobile_no' => 'required|digits:10', 
    //     ]);

    //     if ($validator->fails()) {
    //         return redirect()->route('admin.customer.search.form')
    //                     ->withErrors($validator)
    //                     ->withInput();
    //     }

    //     $mobileNo = $request->input('mobile_no');
    //     $customer = null;

    //     $customer = Customer::where('mobile_number', $mobileNo)->first(); 

    //     return view('admin.customer.search', [
    //         'customer' => $customer,
    //         'mobileNo' => $mobileNo,
    //         'cardNumber' => generateCardNumber(),
    //         'paymentId' => generatePaymentId(),
    //         'services' => Service::all()
    //     ]);
    // }
}
