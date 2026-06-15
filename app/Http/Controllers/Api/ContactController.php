<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Services\SmsService;

class ContactController extends Controller
{
    public function create(Request $request)
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
            'mobile' => $validated['mobile_number']
        ]);

        // if (!empty($validated['mobile_number'])) {

        //     $smsService = new SmsService();

        //     $smsMessageSuccess = $smsService->sendTemplateSms($validated['mobile_number'], 'generate-support-ticket');
        //     if (!$smsMessageSuccess['success']) {
        //         return response([
        //             'success' => false,
        //             'message' => "SMS template not found"
        //         ]);
        //     }
        // }

        return response()->json([
            'success' => true,
            'message' => 'Your inquiry has been submitted, We will contact you within 24-48 hours for a follow-up. Passport Suvidha',
            'data' => [
                'id' => $contact->id,
            ]
        ]);
    }
}
