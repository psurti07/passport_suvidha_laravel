<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer; 
use App\Models\Service;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    /**
     * Display the customer search form.
     *
     * @return \Illuminate\View\View
     */
    public function showSearchForm()
    {
        $customer = null;
        $mobileNo = session('mobileNo');

        if (session('customer_id')) {
            $customer = Customer::find(session('customer_id'));
        }

        return view('admin.customer.search', [
            'customer' => $customer,
            'mobileNo' => $mobileNo,
            'cardNumber' => generateCardNumber(),
            'paymentId' => generatePaymentId(),
            'services' => Service::all()
            
        ]);
    }

    /**
     * Handle the customer search request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function searchCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|digits:10', 
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.customer.search.form')
                        ->withErrors($validator)
                        ->withInput();
        }

        $mobileNo = $request->input('mobile_no');
        $customer = null;

        $customer = Customer::where('mobile_number', $mobileNo)->first(); 
        
        return view('admin.customer.search', [
            'customer' => $customer,
            'mobileNo' => $mobileNo,
            'cardNumber' => generateCardNumber(),
            'paymentId' => generatePaymentId(),
            'services' => Service::all()
        ]);
    }
} 