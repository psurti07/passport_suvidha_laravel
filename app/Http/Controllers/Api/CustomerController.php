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
use App\Models\FbAdsEntry;
use App\Services\ConversionTrackingService;

class CustomerController extends Controller
{

    protected ConversionTrackingService $trakingService;
    public function __construct(ConversionTrackingService $trakingService)
    {
        $this->trakingService = $trakingService;
    }

    public function index(Request $request)
    {
        $query = Customer::query();
        if ($request->has('is_paid')) {
            $query->where('is_paid', filter_var($request->is_paid, FILTER_VALIDATE_BOOLEAN));
        }
        $customers = $query->latest()->paginate(10);
        return response()->json($customers);
    }

    // public function store(Request $request)
    // {
    //     $baseRules = [
    //         'full_name' => 'required|string|max:255',
    //         'mobile_number' => [
    //             'required',
    //             'string',
    //             'max:20',
    //             Rule::unique('customers', 'mobile_number')
    //                 ->where('is_paid', 1),
    //         ],
    //         'email' => [
    //             'required',
    //             'email',
    //             Rule::unique('customers', 'email')
    //                 ->where('is_paid', 1),
    //         ],
    //         'is_paid' => 'sometimes|boolean',
    //     ];

    //     $paidRules = [
    //         'address' => 'required|string',
    //         'gender' => 'required|in:male,female,other',
    //         'date_of_birth' => 'required|date',
    // 'place_of_birth' => 'required|string|max:255',
    //         'education_qualification' => 'required|string|max:255',
    //         'employment_type' => 'required|string|max:255',
    //         'nationality' => 'required|string|max:255',
    //         'payment_info_id' => 'required|numeric',
    //         'service_code' => 'required|string|max:255',
    //     ];

    //     $isPaid = $request->input('is_paid', false) ||
    //         $request->filled(array_keys($paidRules));

    //     $rules = $baseRules;
    //     if ($isPaid) {
    //         $rules = array_merge($rules, $paidRules);
    //     }

    //     $validator = Validator::make($request->all(), $rules);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $data = $validator->validated();
    //     $data['is_paid'] = $isPaid;

    //     $nullableFields = ['address', 'gender', 'date_of_birth', 'nationality', 'education_qualification', 'employment_type', 'payment_info_id', 'service_code'];
    //     foreach ($nullableFields as $field) {
    //         if (!isset($data[$field])) {
    //             $data[$field] = null;
    //         }
    //     }

    //     $customer = Customer::create($data);
    //     return response()->json($customer, 201);
    // }

    public function show(Customer $customer)
    {
        return response()->json($customer);
    }

    public function update(Request $request, Customer $customer)
    {
        // $isPaid = $request->input('is_paid', $customer->is_paid);

        // $rules = [
        //     'full_name' => 'required|string|max:255',
        //     'mobile_number' => [
        //         'required',
        //         'string',
        //         'max:20',
        //         Rule::unique('customers', 'mobile_number')
        //             ->ignore($customer->id)
        //             ->where(function ($query) {
        //                 return $query
        //                     ->whereNull('deleted_at')
        //                     ->where('is_paid', 1);
        //             }),
        //     ],
        //     'email' => [
        //         'required',
        //         'email',
        //         Rule::unique('customers', 'email')
        //             ->ignore($customer->id)
        //             ->where(function ($query) {
        //                 return $query
        //                     ->whereNull('deleted_at')
        //                     ->where('is_paid', 1);
        //             }),
        //     ],
        //     'address' => 'nullable|string',
        //     'gender' => 'nullable|in:male,female,other',
        //     'date_of_birth' => 'nullable|date',
        //     'place_of_birth' => 'nullable|string|max:255',
        //     'education_qualification' => 'required|string|max:255',
        //     'employment_type' => 'required|string|max:255',
        //     'nationality' => 'nullable|string|max:255',
        //     'service_code' => 'nullable|string|max:255',
        // ];

        // if ($isPaid) {
        //     $rules['address'] = 'required|string';
        //     $rules['gender'] = 'required|in:male,female,other';
        //     $rules['date_of_birth'] = 'required|date';
        //     $rules['place_of_birth'] = 'required|string|max:255';
        //     $rules['education_qualification'] = 'required|string|max:255';
        //     $rules['employment_type'] = 'required|string|max:255';
        //     $rules['nationality'] = 'required|string|max:255';
        // }

        // $validator = Validator::make($request->all(), $rules);

        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()], 422);
        // }

        // $data = $validator->validated();

        // $data['is_paid'] = $request->has('is_paid')
        //     ? $request->input('is_paid')
        //     : $customer->is_paid;

        // $customer->update($data);
        $rules = [
            'full_name' => 'required|string|max:255',
            'mobile_number' => [
                'required',
                'regex:/^[6-9][0-9]{9}$/',
                Rule::unique('customers', 'mobile_number')
                    ->ignore($customer->id)
                    ->whereNull('deleted_at'),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('customers', 'email')
                    ->ignore($customer->id)
                    ->whereNull('deleted_at'),
            ],
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'marital_status' => [
                'required',
                Rule::in([
                    'single',
                    'married',
                    'widow',
                    'widower',
                    'separated',
                    'divorced',
                ]),
            ],
            'spouse_name' => 'required_if:marital_status,married|nullable|string|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_mobile' => [
                'required',
                'regex:/^[6-9][0-9]{9}$/',
                'different:mobile_number',
            ],
            'emergency_contact_email' => 'required|email|max:255',

            'address' => 'required|string',
            'pin_code' => 'required|string|max:10',
            'police_station_name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'education_qualification' => 'required|string|max:255',
            'employment_type' => 'required|string|max:255',
            'nationality' => 'required|string|max:255'
        ];

        $validatedData = $request->validate($rules);

        $validatedData['spouse_name'] =
            $validatedData['marital_status'] === 'married'
            ? $request->spouse_name
            : null;

        $customer->update($validatedData);

        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(null, 204);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'mobile_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('customers', 'mobile_number')
                    ->where(function ($query) {
                        $query->where('is_paid', 1)
                            ->whereNull('deleted_at');
                    }),
            ],

            'email' => [
                'required',
                'email',
                Rule::unique('customers', 'email')
                    ->where(function ($query) {
                        $query->where('is_paid', 1)
                            ->whereNull('deleted_at');
                    }),
            ],

            'service_code' => 'required|in:NP36,NP60,TP36,TP60',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $service = Service::where('service_code', $request->service_code)->first();

        if (!$service) {
            return response()->json([
                'errors' => [
                    'service_code' => ['Invalid service selected.']
                ]
            ], 422);
        }

        $existingCustomer = Customer::where('mobile_number', $request->mobile_number)->first();

        if ($existingCustomer) {
            if ($existingCustomer->is_paid) {
                return response()->json([
                    'errors' => ['mobile_number' => ['Customer already registered with this mobile number.']]
                ], 422);
            }

            $data = $validator->validated();

            unset($data['service_code']);

            $data['service_id'] = $service->id;

            $existingCustomer->update($data);

            return response()->json([
                'message' => 'Customer information updated successfully',
                'customer' => $existingCustomer->fresh(),
                'registration_step' => $existingCustomer->registration_step,
                'next_step' => $this->getNextStep($existingCustomer->registration_step),
            ], 200);
        }

        $data = $validator->validated();

        unset($data['service_code']);

        $data['service_id'] = $service->id;

        $data['registration_step'] = 2;

        $customer = Customer::create($data);

        if ($customer) {
            $this->traking_lead($customer);
        }

        if ($request->filled('fbclid')) {
            FbAdsEntry::create([
                'customer_id' => $customer->id,
                'fbclid' => $request->fbclid
            ]);
        }

        $token = $customer->createToken('customer-registration-token')->plainTextToken;

        return response()->json([
            'message' => 'Customer information saved successfully',
            'token' => $token,
            'registration_step' => $customer->registration_step,
            'customer' => $customer,
            'next_step' => $this->getNextStep($customer->registration_step),
            'token_type' => 'Bearer',
        ], 201);
    }

    public function addFamilyDetails(Request $request)
    {
        $request->merge([
            'marital_status' => strtolower(trim($request->marital_status))
        ]);

        $validator = Validator::make($request->all(), [
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'marital_status' => [
                'required',
                Rule::in([
                    'single',
                    'married',
                    'widow',
                    'widower',
                    'separated',
                    'divorced',
                ]),
            ],
            'spouse_name' => 'required_if:marital_status,married|nullable|string|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_mobile' => [
                'required',
                'digits:10',
                'different:mobile_number',
            ],
            'emergency_contact_email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = $request->user();

        if ($customer->registration_step < 2) {
            return response()->json([
                'errors' => [
                    'registration' => [
                        'Please complete OTP verification first.'
                    ]
                ]
            ], 422);
        }

        $data = $validator->validated();

        if ($request->marital_status !== 'married') {
            $data['spouse_name'] = null;
        }

        $data['registration_step'] = 3;

        $customer->update($data);

        return response()->json([
            'message' => 'Family details saved successfully.',
            'customer' => $customer->fresh(),
            'next_step' => 'personal_details'
        ], 200);
    }

    public function addPersonalDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string',
            'pin_code' => 'required|string|max:10',
            'police_station_name' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'education_qualification' => 'required|string|max:255',
            'employment_type' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = $request->user();

        if ($customer->registration_step < 3) {
            return response()->json([
                'errors' => ['registration' => ['Please complete Family Details first.']]
            ], 422);
        }

        $data = $validator->validated();


        $data['registration_step'] = 4;
        $data['nationality'] = 'Indian';

        $customer->update($data);

        $smsService = new SmsService();
        $mobileNumber = $customer->mobile_number;
        if (!empty($mobileNumber)) {

            $url = "https://passportsuvidha.com/apply-passport";

            $smsMessage = $smsService->sendTemplateSms($mobileNumber, 'complete-process-sms', [$url]);
            if (!$smsMessage['success']) {
                return response([
                    'success' => false,
                    'message' => "SMS template not found"
                ]);
            }
        }

        return response()->json([
            'message' => 'Personal details saved successfully.',
            'customer' => $customer,
            'next_step' => 'payment'
        ]);
    }

    // public function getCustomerByFbclid(Request $request)
    // {
    //     $fbclid = $request->id;

    //     $fbLead = FbAdsEntry::with(['customer.service'])
    //         ->where('fbclid', $fbclid)
    //         ->first();

    //     if (!$fbLead || !$fbLead->customer) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Customer not found for the provided fbclid.'
    //         ], 404);
    //     }

    //     $customer = $fbLead->customer;
    //     // Remove old tokens
    //     $customer->tokens()->delete();

    //     // Create new token
    //     $token = $customer->createToken('customer-login-token')->plainTextToken;

    //     return response()->json([
    //         'success' => true,
    //         'customer' => $fbLead->customer,
    //         'token' => $token,
    //     ]);
    // }


    public function getCustomerByEncryptId(Request $request)
    {
        try {
            // Encrypted customer ID from request
            $encryptedId = $request->id;

            if (!$encryptedId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer ID is required.'
                ], 400);
            }

            // Decrypt customer ID
            $customerId = decryptData($encryptedId);

            if (!$customerId || !is_numeric($customerId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid customer ID.'
                ], 400);
            }

            // Get customer
            $customer = Customer::with('service')
                ->find($customerId);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found.'
                ], 404);
            }

            // Remove old tokens
            $customer->tokens()->delete();

            // Create new token
            $token = $customer
                ->createToken('customer-login-token')
                ->plainTextToken;

            return response()->json([
                'success' => true,
                'customer' => $customer,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired customer ID.'
            ], 400);
        }
    }

    public function checkUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|string|min:10|max:15',
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $mobile_number = Customer::where('mobile_number', $request->mobile_number)->where('is_paid', 1)->first();

        if ($mobile_number) {
            return response()->json([
                'errors' => ['mobile_number' => ['Customer already registered with this mobile number.']]
            ], 422);
        }

        $email = Customer::where('email', $request->email)->where('is_paid', 1)->first();

        if ($email) {
            return response()->json([
                'errors' => ['email' => ['Customer already registered with this email.']]
            ], 422);
        }

        return response()->json([
            'message' => "User proceed to registration",
            'status' => 200,
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|string|min:10|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $mobileNumber = $request->mobile_number;

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

        if ($customer->registration_step < 5 || $customer->is_paid == 0) {
            return response()->json([
                'errors' => ['registration' => ['Please complete your registration process first.']]
            ], 422);
        }

        if ($customer->deleted_at !== null) {
            return response()->json([
                'errors' => ['account' => ['Your account has been deleted. Please contact support.']]
            ], 403);
        }

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
            2 => 'family_details',
            3 => 'personal_details',
            4 => 'payment',
            default => 'start',
        };
    }

    protected function traking_lead(Customer $customer)
    {
        try {

            $userResponse = $this->trakingService->userTrack([

                "phoneNumber" => $customer["mobile_number"],
                "countryCode" => "+91",
                "traits" => [
                    "name" => $customer['full_name']
                ],
                "tags" => ["Lead Gen"]

            ]);

            $eventResponse = $this->trakingService->eventTrack(
                [
                    "phoneNumber" => $customer["mobile_number"],
                    "countryCode" => "+91",
                    "event" => "Lead Gen"
                ]
            );
        } catch (\Exception $e) {
            Log::error('Interakt Tracking Failed', [
                'message' => $e->getMessage()
            ]);
        }
    }
}
