<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BrevoMailService;
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
use Laravel\Sanctum\PersonalAccessToken;

class SupportTicketController extends Controller
{
  protected $brevoMailServices;
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
  public function store(Request $request, BrevoMailService $brevoMailService)
  {
    $customer = Auth::guard('sanctum')->user();

    $rules = [
      'subject' => 'required|string|max:255',
      'message' => 'required|string',
    ];

    $ticketData = [];

    if ($customer instanceof Customer) {

      $validator = Validator::make(
        $request->only(['subject', 'message']),
        $rules
      );

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      $validatedData = $validator->validated();

      $customerName = trim(
        "{$customer->first_name} {$customer->last_name}"
      );

      if (empty($customerName)) {
        $emailParts = explode('@', $customer->email ?? '');
        $customerName = $emailParts[0] ?: "Customer {$customer->id}";
      }

      $ticketData = [
        'customer_id' => $customer->id,
        'name'        => $customerName,
        'email'       => $customer->email,
        'subject'     => $validatedData['subject'],
        'message'     => $validatedData['message'],
      ];

      $mobileNumber = $customer->mobile_number ?? null;
    } else {

      $rules['name'] = 'required|string|max:255';
      $rules['email'] = 'required|email|max:255';
      $rules['mobile_number'] = 'required|digits:10';

      $validator = Validator::make($request->all(), $rules);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      $validatedData = $validator->validated();

      $ticketData = [
        'customer_id' => null,
        'name'        => $validatedData['name'],
        'email'       => $validatedData['email'],
        'subject'     => $validatedData['subject'],
        'message'     => $validatedData['message'],
      ];

      $mobileNumber = $validatedData['mobile_number'];
    }

    // Create Ticket First
    $ticket = Ticket::create($ticketData);

    /*
    |--------------------------------------------------------------------------
    | Send Email
    |--------------------------------------------------------------------------
    */
    try {

      if (!empty($ticketData['email'])) {

        $supportTicketMail = view('emails.support', [
          'ticketData' => $ticketData,
          'ticket'     => $ticket,
          'subject' => $ticket->subject,
        ])->render();

        $brevoMailService->sendBrevoHtmlMail(
          $ticketData['email'],
          $ticketData['name'],
          'Support Ticket Received - #' . $ticket->id,
          $supportTicketMail
        );
      }
    } catch (\Exception $e) {

      Log::error('SUPPORT TICKET EMAIL FAILED', [
        'message'     => $e->getMessage(),
        'file'        => $e->getFile(),
        'line'        => $e->getLine(),
        'ticket_id'   => $ticket->id ?? null,
        'customer_id' => $ticket->customer_id ?? null,
        'trace'       => $e->getTraceAsString(),
      ]);
    }
    /*
    |--------------------------------------------------------------------------
    | Send SMS
    |--------------------------------------------------------------------------
    */
    try {

      if (!empty($mobileNumber)) {

        $smsService = new SmsService();

        $smsMessageSuccess = $smsService->sendTemplateSms($customer->mobile_number, 'generate-support-ticket');
        if (!$smsMessageSuccess['success']) {
          return response([
            'success' => false,
            'message' => "SMS template not found"
          ]);
        }
      }
    } catch (\Exception $e) {

      Log::error('SMS FAILED', [
        'ticket_id' => $ticket->id,
        'message'   => $e->getMessage(),
      ]);
    }

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

    $ticket = Ticket::where('ticket_number', $ticket_number)->first();

    if (!$ticket) {
      return response()->json(['error' => 'Ticket not found.'], 404);
    }

    if (!$customer instanceof Customer || $ticket->customer_id !== $customer->id) {

      return response()->json(['error' => 'Unauthorized to view this ticket.'], 403);
    }

    $ticket->load('ticketRemarks.user');

    // Return the ticket using TicketResource
    return new TicketResource($ticket);
  }

  public function storePublic(Request $request)
  {
    $customer = null;

    if ($request->bearerToken()) {
      $accessToken = PersonalAccessToken::findToken($request->bearerToken());

      if ($accessToken && $accessToken->tokenable instanceof \App\Models\Customer) {
        $customer = $accessToken->tokenable;
      }
    }
    $rules = [
      'subject' => 'required|string|max:255',
      'message' => 'required|string',
    ];

    // Guest validation
    if (!$customer) {
      $rules['name'] = 'required|string|max:255';
      $rules['email'] = 'required|email|max:255';
      $rules['mobile_number'] = 'required|digits:10';
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'errors' => $validator->errors()
      ], 422);
    }

    $validated = $validator->validated();

    if ($customer) {

      $name = trim("{$customer->first_name} {$customer->last_name}");

      if (empty($name)) {
        $name = explode('@', $customer->email)[0] ?? "Customer";
      }

      $ticketData = [
        'customer_id' => $customer->id,
        'name' => $name,
        'email' => $customer->email,
        'mobile_number' => $customer->mobile_number ?? $request->mobile_number,
        'subject' => $validated['subject'],
        'message' => $validated['message'],
        'status' => 'open',
      ];

      $mobileNumber = $ticketData['mobile_number'];
    } else {

      $ticketData = [
        'customer_id' => null,
        'name' => $validated['name'],
        'email' => $validated['email'],
        'mobile_number' => $validated['mobile_number'] ?? null,
        'subject' => $validated['subject'],
        'message' => $validated['message'],
        'status' => 'open',
      ];

      $mobileNumber = $ticketData['mobile_number'];
      $name = $validated['name'];
    }

    $ticket = Ticket::create($ticketData);


    return response()->json([
      'success' => true,
      'message' => 'Ticket created successfully',
      'data' => [
        'ticket_number' => $ticket->ticket_number,
        'status' => $ticket->status,
      ]
    ]);
  }
}
