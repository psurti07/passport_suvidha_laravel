<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    // public function create(Request $request, BrevoMailService $brevoMailService)
    // {
    //     $validated = $request->validate([
    //         'first_name' => 'required|string',
    //         'last_name' => 'required|string',
    //         'email' => 'required|email|max:255',
    //         'subject' => 'required|string|max:255',
    //         'message' => 'required|string|max:255',
    //         'mobile_number' => 'required|digits:10',
    //     ]);

    //     $contact = Contact::create([
    //         'first_name' => $validated['first_name'],
    //         'last_name' => $validated['last_name'],
    //         'email' => $validated['email'],
    //         'subject' => $validated['subject'],
    //         'message' => $validated['message'],
    //         'mobile' => $validated['mobile_number']
    //     ]);

    //     // if (!empty($validated['mobile_number'])) {

    //     //     $smsService = new SmsService();

    //     //     $smsMessageSuccess = $smsService->sendTemplateSms($validated['mobile_number'], 'generate-support-ticket');
    //     //     if (!$smsMessageSuccess['success']) {
    //     //         return response([
    //     //             'success' => false,
    //     //             'message' => "SMS template not found"
    //     //         ]);
    //     //     }
    //     // }

    //     /*
    // |--------------------------------------------------------------------------
    // | Send Email
    // |--------------------------------------------------------------------------
    // */
    //     try {

    //         if (!empty($ticketData['email'])) {

    //             $supportTicketMail = view('emails.support', [
    //                 'ticketData' => $contact,
    //                 'ticket'     => $ticket,
    //             ])->render();

    //             $brevoMailService->sendBrevoHtmlMail(
    //                 $ticketData['email'],
    //                 $ticketData['name'],
    //                 'Support Ticket Received - #' . $ticket->id,
    //                 $supportTicketMail
    //             );
    //         }
    //     } catch (\Exception $e) {

    //         Log::error('SUPPORT TICKET EMAIL FAILED', [
    //             'message'     => $e->getMessage(),
    //             'file'        => $e->getFile(),
    //             'line'        => $e->getLine(),
    //             'ticket_id'   => $ticket->id ?? null,
    //             'customer_id' => $ticket->customer_id ?? null,
    //             'trace'       => $e->getTraceAsString(),
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Your inquiry has been submitted, We will contact you within 24-48 hours for a follow-up. Passport Suvidha',
    //         'data' => [
    //             'id' => $contact->id,
    //         ]
    //     ]);
    // }

    public function create(Request $request, BrevoMailService $brevoMailService)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:255',
            'mobile_number' => 'required|digits:10',
        ]);

        $contact = Contact::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'mobile' => $validated['mobile_number'],
        ]);

        try {

            if (!empty($contact->email)) {

                $contactMail = view('emails.support', [
                    'ticketData' => ['name' => $contact->first_name . ' ' . $contact->last_name,],
                    'ticket' => null,
                    'subject' => $contact->subject,
                ])->render();

                $response = $brevoMailService->sendBrevoHtmlMail($contact->email, $contact->first_name . ' ' . $contact->last_name, 'Contact Enquiry Received', $contactMail);
                if (!$response['success']) {

                    Log::error('CONTACT ENQUIRY EMAIL FAILED', [
                        'contact_id' => $contact->id,
                        'response' => $response,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('CONTACT ENQUIRY EMAIL ERROR', [
                'contact_id' => $contact->id ?? null,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Your inquiry has been submitted, We will contact you within 24-48 hours for a follow-up. Passport Suvidha',
            'data' => ['id' => $contact->id,]
        ]);
    }
}
