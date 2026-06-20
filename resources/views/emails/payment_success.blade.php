@extends('emails.layouts.master')

@section('content')
    <h2 style="margin:0 0 20px;font-size:24px;color:#003366;">
        Payment Successful </h2>


    <p style="font-size:15px;color:#555;">
        Dear <b>{{ $customer->full_name }}</b>
    </p>

    <p style="font-size:15px;color:#555;line-height:20px;">
        Thank you for your payment. Your transaction has been completed successfully and your application has been submitted
        for processing.
    </p>

    <!-- CARD -->
    <div style="background:#f7f9fc;border:1px solid #e3e8ef;border-radius:10px;padding:20px;">
        <p style="font-size:13px;color:#444;line-height:20px;margin:0;">
            Your payment has been received successfully.
            Your application is now under process and our team will keep you updated regarding its status through email and
            SMS notifications.
            We appreciate your trust in Passport Suvidha.
        </p>
    </div>

    <br>

    <p style="font-size:15px;color:#555;line-height:22px;">
        Thank you,<br>
        <b>Passport Suvidha Team</b>
    </p>
@endsection
