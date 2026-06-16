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
  public function store(Request $request)
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

        $html = '
            <!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Support Ticket Received</title>
  </head>

  <body
    style="
      margin: 0;
      padding: 20px 10px;
      background: #eef2f7;
      font-family: Arial, Helvetica, sans-serif;
    "
  >
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
      <tr>
        <td align="center">

          <!-- MAIN CONTAINER -->
          <table
            width="100%"
            cellpadding="0"
            cellspacing="0"
            border="0"
            style="
              max-width: 600px;
              background: #ffffff;
              border-radius: 14px;
              overflow: hidden;
              box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            "
          >

            <!-- HEADER -->
            <tr>
              <td
                align="center"
                style="background: rgb(0 51 102); padding: 18px;"
              >
                <img
                  src="https://passportsuvidha.com/logo/logo.png"
                  width="120"
                  alt="Passport Suvidha"
                  style="display:block; padding-top:20px"
                />
              </td>
            </tr>

            <!-- BODY -->
            <tr>
              <td style="padding: 34px 38px;">

                <!-- TITLE -->
                <div
                  style="
                    font-size: 26px;
                    font-weight: 700;
                    color: #002c85;
                    margin-bottom: 12px;
                  "
                >
                  Support Ticket Received
                </div>

                <!-- DESCRIPTION -->
                <div style="color:#4d5b72;font-size:15px;line-height:26px;">
                  Dear <strong>{{customer_name}}</strong>,<br><br>

                  Thank you for contacting Passport Suvidha.
                  We have successfully received your support request.
                </div>

                <!-- DIVIDER -->
                <div style="border-top:1px dashed #d8e0ed;margin:24px 0;"></div>

                <!-- TICKET BOX -->
                <table
                  width="100%"
                  cellpadding="0"
                  cellspacing="0"
                  border="0"
                  style="
                    border: 1px solid #dbe6ff;
                    background: #f8fbff;
                    border-radius: 10px;
                  "
                >
                  <tr>
                    <td style="padding: 18px 22px;">

                      <!-- TICKET ID -->
                      <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                          <td style="font-size:14px;color:#1e2a3d;">
                            <strong>Ticket ID:</strong> {{ticket_id}}
                          </td>
                        </tr>
                      </table>

                      <div style="height:14px;"></div>

                      <!-- SUBJECT -->
                      <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                          <td style="font-size:14px;color:#1e2a3d;">
                            <strong>Subject:</strong> {{ticket_subject}}
                          </td>
                        </tr>
                      </table>

                      <div style="height:14px;"></div>

                      <!-- DATE -->
                      <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                          <td style="font-size:14px;color:#1e2a3d;">
                            <strong>Date:</strong> {{ticket_date}}
                          </td>
                        </tr>
                      </table>

                    </td>
                  </tr>
                </table>

                <!-- NOTE -->
                <div style="margin-top:20px;color:#4d5b72;font-size:15px;">
                  Our support team will contact you shortly.
                </div>

                <div style="margin-top:18px;color:#3d4b63;font-size:14px;">
                  Regards,<br>
                  <strong>Passport Suvidha Support Team</strong>
                </div>

              </td>
            </tr>

            <!-- FOOTER -->
            <tr>
              <td
                align="center"
                style="background: rgba(0, 0, 0, 0); padding: 20px;"
              >
                <div style="color:#4d5b72;font-size:12px;">
                  © {{year}} Passport Suvidha. All rights reserved.
                </div>
              </td>
            </tr>

          </table>

        </td>
      </tr>
    </table>
  </body>
</html>
            ';

        $html = str_replace(
          [
            '{{customer_name}}',
            '{{ticket_id}}',
            '{{ticket_subject}}',
            '{{ticket_date}}',
            '{{year}}'
          ],
          [
            $ticketData['name'],
            $ticket->id,
            $ticket->subject,
            now()->format('d M Y h:i A'),
            date('Y')
          ],
          $html
        );

        $brevoMailService = new BrevoMailService();

        $response = $brevoMailService->sendBrevoHtmlMail(
          $ticketData['email'],
          $ticketData['name'],
          'Support Ticket Received - #' . $ticket->id,
          $html
        );

        if (!$response['success']) {
          Log::error('BREVO MAIL FAILED', [
            'ticket_id' => $ticket->id,
            'response'  => $response,
          ]);
        }
      }
    } catch (\Exception $e) {

      Log::error('BREVO MAIL ERROR', [
        'ticket_id' => $ticket->id,
        'message'   => $e->getMessage(),
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
