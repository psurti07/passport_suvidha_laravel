<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\SmsService;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Customer::query();
        if ($request->has('is_paid')) {
            $query->where('is_paid', filter_var($request->is_paid, FILTER_VALIDATE_BOOLEAN));
        }
        $customers = $query->latest()->paginate(10);
        return response()->json($customers);
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
            'mobile_number' => 'required|string|max:20',
            'email' => 'required|email|unique:customers,email',
            'is_paid' => 'sometimes|boolean',
        ];

        $paidRules = [
            'address' => 'required|string',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'payment_info_id' => 'required|numeric',
            'service_code' => 'required|string|max:255',
        ];

        $isPaid = $request->input('is_paid', false) || 
                  $request->filled(array_keys($paidRules));

        $rules = $baseRules;
        if ($isPaid) {
            $rules = array_merge($rules, $paidRules);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['is_paid'] = $isPaid;

        $nullableFields = ['address', 'gender', 'date_of_birth', 'place_of_birth', 'nationality', 'payment_info_id', 'service_code'];
        foreach ($nullableFields as $field) {
            if (!isset($data[$field])) {
                $data[$field] = null;
            }
        }

        $customer = Customer::create($data);
        return response()->json($customer, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return response()->json($customer);
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
        $isPaid = $request->input('is_paid', $customer->is_paid);
        
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'email' => ['required', 'email', Rule::unique('customers')->ignore($customer->id)],

            'address' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'place_of_birth' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'service_code' => 'nullable|string|max:255',
        ];

        if ($isPaid) {
            $rules['address'] = 'required|string';
            $rules['gender'] = 'required|in:male,female,other';
            $rules['date_of_birth'] = 'required|date';
            $rules['place_of_birth'] = 'required|string|max:255';
            $rules['nationality'] = 'required|string|max:255';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $data['is_paid'] = $request->has('is_paid')
            ? $request->input('is_paid')
            : $customer->is_paid;

        $customer->update($data);

        return response()->json($customer);
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
        return response()->json(null, 204);
    }

    /**
     * Create a customer with basic information and send OTP
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile_number' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if customer already exists with this mobile number
        $existingCustomer = Customer::where('mobile_number', $request->mobile_number)->first();
        
        if ($existingCustomer) {
            // If customer exists and is paid, return error
            if ($existingCustomer->is_paid) {
                return response()->json([
                    'errors' => ['mobile_number' => ['Customer already registered with this mobile number.']]
                ], 422);
            }
            
            // If customer exists but not paid, update their info
            $data = $validator->validated();
            // $data['registration_step'] = 1; // Reset to step 1
            
            // If email is changing, validate it's unique
            if ($existingCustomer->email !== $request->email) {
                $emailValidator = Validator::make(['email' => $request->email], [
                    'email' => 'required|email|unique:customers,email'
                ]);
                
                if ($emailValidator->fails()) {
                    return response()->json(['errors' => $emailValidator->errors()], 422);
                }
            }
            
            $existingCustomer->update($data);
            
            return response()->json([
                'message' => 'Customer information updated successfully',
                'customer' => $existingCustomer,
                // 'next_step' => 'otp_verification'
                'registration_step' => $existingCustomer->registration_step,
                'next_step' => $this->getNextStep($existingCustomer->registration_step)
            ], 200);
        }
        
        // Create new customer
        $data = $validator->validated();
        
        // Validate unique email for new customer
        $emailValidator = Validator::make(['email' => $request->email], [
            'email' => 'unique:customers,email'
        ]);
        
        if ($emailValidator->fails()) {
            return response()->json(['errors' => $emailValidator->errors()], 422);
        }
        
        $data['registration_step'] = 1; // Step 1: Basic information
        
        $customer = Customer::create($data);

        return response()->json([
            'message' => 'Customer information saved successfully',
            'customer' => $customer,
            'next_step' => 'otp_verification'
        ], 201);
    }

    /**
     * Add additional customer information (Step 3)
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addAdditionalInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string',
            'pin_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get customer via auth
        $customer = $request->user();

        // Verify registration step
        if ($customer->registration_step < 2) {
            return response()->json([
                'errors' => ['registration' => ['Please complete OTP verification first.']]
            ], 422);
        }

        // Update customer with additional info
        $data = $validator->validated();
        
        
        $data['registration_step'] = 3; // Step 3: Additional info
        
        $customer->update($data);

        $smsService = new SmsService();
            $mobileNumber = $customer->mobile_number;
            if (!empty($mobileNumber)) {

                    $message = "Dear Customer, Your Passport Application is almost done! Complete process now to live your travel dreams. Click Now https://passportsuvidha.com/apply-passport";

                    $response = $smsService->sendSms($mobileNumber, $message);
                }

        return response()->json([
            'message' => 'Additional information saved successfully',
            'customer' => $customer,
            'next_step' => 'service_selection'
        ]);
    }

    /**
     * Select service for customer (Step 4)
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function selectService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_size' => 'required|string|max:10',
            'passport_type' => 'required|string|max:10',
            'nationality' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = $request->user();

        if ($customer->registration_step < 3) {
            return response()->json([
                'errors' => ['registration' => ['Please complete additional information first.']]
            ], 422);
        }

        $passportType = strtolower($request->passport_type);
        $bookSize = $request->book_size;

        $serviceCode = match (true) {
            $passportType === 'normal' && $bookSize == '36' => 'NP36',
            $passportType === 'normal' && $bookSize == '60' => 'NP60',
            $passportType === 'tatkal' && $bookSize == '36' => 'TP36',
            $passportType === 'tatkal' && $bookSize == '60' => 'TP60',
            default => null,
        };

        if (!$serviceCode) {
            return response()->json([
                'error' => 'Invalid selection'
            ], 422);
        }

        $service = Service::where('service_code', $serviceCode)->first();

        if (!$service) {
            return response()->json([
                'error' => 'Service not found'
            ], 404);
        }

        $customer->update([
            'service_id' => $service->id,
            // 'book_size' => $bookSize,
            // 'passport_type' => $passportType,
            'nationality' => $request->nationality,
            'registration_step' => 4,
        ]);

        return response()->json([
            'message' => 'Service selected successfully',
            'customer' => $customer,
            'next_step' => 'payment'
        ]);
    }
    /**
     * Handle login request for customers 
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|string|min:10|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $mobileNumber = $request->mobile_number;
        
        // Check if customer exists
        $customer = Customer::where('mobile_number', $mobileNumber)->first();
        
        if (!$customer) {
            return response()->json([
                'errors' => ['mobile_number' => ['Customer not found with this mobile number.']]
            ], 404);
        }

        if ($customer->is_active == 0) {
            return response()->json([
                'errors' => ['account' => ['Your account is inactive.']]
            ], 403);
        }
        
        // Check if the customer has completed registration
        if ($customer->registration_step < 4) {
            return response()->json([
                'errors' => ['registration' => ['Please complete your registration process first.']]
            ], 422);
        }

        if ($customer->deleted_at !== null) {
            return response()->json([
                'errors' => ['account' => ['Your account has been deleted. Please contact support.']]
            ], 403);
        }
        
        // Return success response with next step to request OTP
        return response()->json([
            'message' => 'Customer found, proceed with OTP verification.',
            'customer' => [
                'id' => $customer->id,
                'mobile_number' => $customer->mobile_number,
                'full_name' => $customer->full_name
            ],
            'next_step' => 'otp_verification'
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    private function getNextStep($step)
    {
        return match ($step) {
            1 => 'otp_verification',
            2 => 'additional_information',
            3 => 'service_selection',
            4 => 'payment',
            default => 'start',
        };
    }
}
