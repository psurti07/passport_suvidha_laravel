@extends('emails.layouts.master')

@section('content')
    <h2 style="margin:0 0 20px;font-size:24px;color:#003366;">
        Payment Successful
    </h2>

    <p style="font-size:15px;color:#555;">
        Dear <b>{{ $customer->full_name }}</b>,
    </p>

    <p style="font-size:15px;color:#555;line-height:20px;">
        Thank you for your payment. Your transaction has been completed successfully and your application has been submitted
        for processing.
    </p>

    <!-- CARD -->
    <div style="background:#f7f9fc;border:1px solid #e3e8ef;border-radius:10px;padding:20px;">

        <table width="100%" cellpadding="0" cellspacing="0" border="0">

            <!-- Payment Status -->
            <tr class="mobile-block">
                <td class="mobile-label" style="padding:8px 0;width:160px;font-weight:bold;">
                    Payment Status
                </td>
                <td class="mobile-value" style="padding:8px 0;">
                    <span
                        style="background:#e8f8ee;color:#1e7e34;padding:6px 12px;border-radius:20px;display:inline-block;font-weight:600;">
                        Successful
                    </span>
                </td>
            </tr>

            <!-- Invoice -->
            <tr class="mobile-block">
                <td class="mobile-label" style="padding:8px 0;width:160px;font-weight:bold;">
                    Invoice Number
                </td>
                <td class="mobile-value" style="padding:8px 0;">
                    {{ $invoice->inv_no }}
                </td>
            </tr>

            <!-- Payment ID -->
            <tr class="mobile-block">
                <td class="mobile-label" style="padding:8px 0;width:160px;font-weight:bold;">
                    Payment ID
                </td>
                <td class="mobile-value" style="padding:8px 0;">
                    {{ $payment_id }}
                </td>
            </tr>

            <!-- Date -->
            <tr class="mobile-block">
                <td class="mobile-label" style="padding:8px 0;width:160px;font-weight:bold;">
                    Payment Date
                </td>
                <td class="mobile-value" style="padding:8px 0;">
                    {{ \Carbon\Carbon::parse($payment_date)->format('d M Y h:i A') }}
                </td>
            </tr>

            <!-- Amount -->
            <tr class="mobile-block">
                <td class="mobile-label" style="padding:8px 0;width:160px;font-weight:bold;">
                    Amount Paid
                </td>
                <td class="mobile-value" style="padding:8px 0;">
                    ₹{{ number_format($invoice->total_amount, 2) }}
                </td>
            </tr>

        </table>
    </div>

    <br>

    <!-- NOTE BOX -->
    <div
        style="background:#eef6ff;border-left:4px solid #003366;padding:15px;border-radius:4px;font-size:13px;color:#555;line-height:20px;">
        Invoice PDF attached. Keep it for your records.
    </div>

    <br>

    <p style="
    font-size:14px;
    color:#555;
    line-height:20px;">
        Thank you,<br>
        <b>Passport Suvidha Team</b>
    </p>
@endsection
