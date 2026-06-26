@extends('emails.layouts.master')

@section('content')
<h2 style="margin:0 0 20px;font-size:24px;color:#003366;">
    Welcome to Passport Suvidha
</h2>

<p style="font-size:15px;color:#555;">
    Dear <b>{{ $customer->first_name }} {{ $customer->last_name }}</b>,
</p>

<div style="background:#f7f9fc;border:1px solid #e3e8ef;border-radius:10px;padding:25px;">

    <p style="font-size:13px;color:#444;line-height:20px;margin:0;">
        {{ $message }}
    </p>

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