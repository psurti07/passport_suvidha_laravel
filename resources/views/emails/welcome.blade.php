@extends('emails.layouts.master')

@section('content')
    <h2 style="margin:0 0 20px;font-size:24px;color:#003366;">
        Welcome to Passport Suvidha
    </h2>

    <p style="font-size:15px;color:#555;">
        Dear <b>{{ $customer->first_name }} {{ $customer->last_name }}</b>,
    </p>

    <!-- MAIN CARD -->
    <div style="background:#f7f9fc;border:1px solid #e3e8ef;border-radius:10px;padding:25px;">

        <p style="font-size:13px;color:#444;line-height:20px;margin:0;">
            Welcome to <b>Passport Suvidha</b>. We are happy to have you on board.
            Your application has been successfully submitted, and our team has started processing it.
        </p>

        <br>

        <p style="font-size:13px;color:#444;line-height:20px;margin:0;">
            You will receive timely updates regarding your application status via email and SMS.
            Our team is committed to providing you with a smooth and hassle-free experience.
        </p>

        <br>

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
