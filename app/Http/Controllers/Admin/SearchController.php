<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer; // Use Customer model
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    /**
     * Display the customer search form.
     *
     * @return \Illuminate\View\View
     */
    public function showSearchForm()
    {
        return view('admin.customer.search', ['customer' => null, 'mobile_no' => null]);
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
            'mobile_no' => 'required|digits:10', // Keep input name as mobile_no if form uses it
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.customer.search.form')
                        ->withErrors($validator)
                        ->withInput();
        }

        $mobileNo = $request->input('mobile_no');
        $customer = null;

        // Query Customer model using 'mobile_number' column
        $customer = Customer::where('mobile_number', $mobileNo)->first(); 

        return view('admin.customer.search', compact('customer', 'mobileNo'));
    }
} 