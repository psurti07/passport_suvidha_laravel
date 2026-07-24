@extends('emails.layouts.master')

@section('content')
    <h2 style="margin:0 0 20px;font-size:24px;color:#b30000;">
        Payment Failed
    </h2>

    <p style="font-size:15px;color:#555;">
        @if ($customer->full_name)
            Dear <b>{{ $customer->full_name }}</b>,
        @else
            Dear <b>{{ $customer->full_name }}</b>,
        @endif
    </p>

    <p style="font-size:15px;color:#555;line-height:20px;">
        Unfortunately, your payment could not be completed. Your application has not been processed.
    </p>

    <!-- CARD (same design as success email) -->
    <div style="background:#f7f9fc;border:1px solid #e3e8ef;border-radius:10px;padding:20px;">
        <p style="font-size:13px;color:#444;line-height:20px;margin:0;">
            Transaction failed due to bank decline, network issue, or payment interruption.
            No application has been submitted against this transaction.
            Please try again to complete your payment and proceed with your application.</p>
    </div>

    <br>

    <p style="
    font-size:15px;
    color:#555;
    line-height:22px;">
        Thank you,<br>
        <b>Passport Suvidha Team</b>
    </p>
@endsection
