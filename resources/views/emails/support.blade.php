@extends('emails.layouts.master')

@section('content')
    <h2 style="margin:0 0 20px;font-size:24px;color:#003366;">
        {{ !empty($ticket) ? 'Support Ticket Received' : 'Contact Enquiry Received' }}
    </h2>

    <p style="font-size:15px;color:#555;">
        Dear <b>{{ $ticketData['name'] }}</b>,
    </p>

    <p style="font-size:15px;color:#555;line-height:22px;">
        Thank you for contacting Passport Suvidha.
        We have successfully received your request.
    </p>

    <div style="background:#f7f9fc;border:1px solid #e3e8ef;border-radius:10px;padding:20px;">

        <table width="100%" cellpadding="0" cellspacing="0" border="0">

            @if (!empty($ticket) && !empty($ticket->id))
                <tr class="mobile-block">
                    <td class="mobile-label" style="padding:8px 0;width:160px;font-weight:bold;">
                        Ticket ID
                    </td>
                    <td class="mobile-value" style="padding:8px 0;">
                        #{{ $ticket->id }}
                    </td>
                </tr>
            @endif

            <tr class="mobile-block">
                <td class="mobile-label" style="padding:8px 0;width:160px;font-weight:bold;">
                    Subject
                </td>
                <td class="mobile-value" style="padding:8px 0;">
                    {{ $ticket->subject ?? ($contact->subject ?? ($subject ?? 'General Enquiry')) }}
                </td>
            </tr>

            <tr class="mobile-block">
                <td class="mobile-label" style="padding:8px 0;width:160px;font-weight:bold;">
                    Date
                </td>
                <td class="mobile-value" style="padding:8px 0;">
                    {{ now()->format('d M Y h:i A') }}
                </td>
            </tr>

            @if (!empty($ticket) && !empty($ticket->priority))
                <tr class="mobile-block">
                    <td class="mobile-label" style="padding:8px 0;width:160px;font-weight:bold;">
                        Priority
                    </td>
                    <td class="mobile-value" style="padding:8px 0;">
                        {{ ucfirst($ticket->priority) }}
                    </td>
                </tr>
            @endif

        </table>
    </div>

    <br>

    <div
        style="background:#eef6ff;border-left:4px solid #003366;padding:15px;border-radius:4px;font-size:13px;color:#555;line-height:20px;">
        @if (!empty($ticket) && !empty($ticket->id))
            Our support team will review your ticket and contact you shortly.
            Please keep your Ticket ID handy for future communication.
        @else
            Our team will review your enquiry and contact you shortly.
        @endif
    </div>

    <br>

    <p style="font-size:14px;color:#555;line-height:20px;">
        Regards,<br>
        <b>Passport Suvidha Support Team</b>
    </p>
@endsection
