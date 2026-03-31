<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Ticket;
use App\Http\Resources\TicketResource;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Services\SmsService;
use Illuminate\Support\Facades\Mail;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of the authenticated customer's tickets.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // Explicitly use the 'sanctum' guard (or your specific customer guard name)
        $customer = Auth::guard('sanctum')->user(); // <-- Use Auth facade and specify guard

        // Check if authentication was successful and if the user is a Customer
        if (!$customer instanceof Customer) {
             // Handle cases where the authenticated entity is not a customer
             // Or if using specific guard: $customer = Auth::guard('customer_api')->user();
             // If still null, return error
             return response()->json(['error' => 'Customer authentication required.'], 401);
        }

        // Fetch tickets for the authenticated customer using customer_id
        // *** IMPORTANT: Assumes Ticket model has a 'customer_id' foreign key referencing customers.id ***
        $tickets = Ticket::where('customer_id', $customer->id)
                        ->latest()
                        ->paginate();

        return TicketResource::collection($tickets);
    }

    /**
     * Store a newly created ticket in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|TicketResource
     */
    public function store(Request $request)
    {
        // Explicitly use the 'sanctum' guard
        $customer = Auth::guard('sanctum')->user(); // Returns Customer instance or null

        // Initialize rules and ticket data
        $rules = [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ];
        $ticketData = [];

        if ($customer instanceof Customer) {
            // Authenticated Customer
            // No need for name/email rules, we'll use the customer's data

            // Validate only subject and message for authenticated customers
            $validator = Validator::make($request->only(['subject', 'message']), $rules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Get validated subject and message
            $validatedData = $validator->validated();

            // *** IMPORTANT: Assumes Ticket model has a 'customer_id' foreign key ***
            $ticketData['customer_id'] = $customer->id; // <-- This line assigns the customer ID

            // Use Customer's full name
            $customerName = trim("{$customer->first_name} {$customer->last_name}");
            if (empty($customerName)) {
                 // Fallback if name parts are empty/null
                 $emailParts = explode('@', $customer->email ?? '');
                 $customerName = $emailParts[0] ?: "Customer {$customer->id}";
            }
            $ticketData['name'] = $customerName;
            $ticketData['email'] = $customer->email;
            $ticketData['subject'] = $validatedData['subject'];
            $ticketData['message'] = $validatedData['message'];

        } else {
           // Guest User (Not authenticated or not a Customer instance)
            $rules['name'] = 'required|string|max:255';
            $rules['email'] = 'required|email|max:255';
            $rules['mobile_number'] = 'required|digits:10'; // ✅ ADD THIS
            
            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            
            $validatedData = $validator->validated();
            
            $ticketData['customer_id'] = null;
            $ticketData['name'] = $validatedData['name'];
            $ticketData['email'] = $validatedData['email'];
            $ticketData['subject'] = $validatedData['subject'];
            $ticketData['message'] = $validatedData['message'];
            
            // ✅ store mobile for SMS
            $mobileNumber = $validatedData['mobile_number'];
        }
            
            //
            // CREATE TICKET
            //
            $ticket = Ticket::create($ticketData);
            
            $smsService = new SmsService();
            
            try {
            
                // ✅ determine mobile + name for BOTH cases
                if ($customer instanceof Customer) {
                    $mobileNumber = $customer->mobile_number;
                    $name = $ticketData['name'];
                }

                // =========================
                // 📧 SEND EMAIL
                // =========================
                if (!empty($ticketData['email'])) {
                    Mail::raw(
                        "Hello $name,\n\nYour support ticket (#{$ticket->id}) has been created successfully.\n\nSubject: {$ticket->subject}\n\nWe will get back to you soon.\n\nThank you.",
                        function ($message) use ($ticketData) {
                            $message->to($ticketData['email'])
                                    ->subject('Support Ticket Created');
                        }
                    );
                }

                // if (!empty($mobileNumber)) {

        // ✅ FIXED message (no #, no undefined variable)

        // $message = "Hello, 123456 is the OTP for logging into your Passport Suvidha account. Please don't share this with others. Thank you.";

        // $response = $smsService->sendSms($mobileNumber, $message);

        // Log::info('TICKET SMS RESULT', $response);
        //         }
            
            } catch (\Exception $e) {
                Log::error("SMS Failed: " . $e->getMessage());
            }
            
            // Return response
            return new TicketResource($ticket);
    }

    // TODO: Add show, update, destroy methods as needed
    // Example show method:
    /*
    public function show(Request $request, Ticket $ticket)
    {
        // Ensure the authenticated user owns the ticket or handle authorization
        // Example using Policy:
        // if ($request->user()->cannot('view', $ticket)) {
        //     abort(403);
        // }
        // Or direct check:
        // if ($ticket->user_id !== $request->user()->id) {
        //     abort(403); // Or return appropriate response
        // }
        return new TicketResource($ticket);
    }
    */

    /**
     * Display the specified ticket by its ticket number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $ticket_number  // Changed from Ticket $ticket to string $ticket_number
     * @return \App\Http\Resources\TicketResource|\Illuminate\Http\JsonResponse
     */
    // Changed signature: removed Ticket type hint, use $ticket_number parameter
    public function show(Request $request, $ticket_number)
    {
        $customer = Auth::guard('sanctum')->user();

        // Find the ticket by ticket_number first
        $ticket = Ticket::where('ticket_number', $ticket_number)->first();

        // Check if ticket exists
        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found.'], 404);
        }

        // Check if authenticated user is a customer and owns the ticket
        if (!$customer instanceof Customer || $ticket->customer_id !== $customer->id) {
            // Return 403 Forbidden if not authorized
            return response()->json(['error' => 'Unauthorized to view this ticket.'], 403);
        }

        // Eager load the remarks and the user associated with each remark
        $ticket->load('ticketRemarks.user');

        // Return the ticket using TicketResource
        return new TicketResource($ticket);
    }
} 