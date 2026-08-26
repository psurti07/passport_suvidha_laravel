@extends('emails.layouts.master')

@section('content')
    <h2 style="
        margin:0 0 20px;
        font-size:24px;
        color:#0f2a43;
    ">
        Passport Seva Login Credentials
    </h2>

    <p style="
        font-size:15px;
        color:#555;
        margin:0 0 15px;
    ">
        Dear <b style="color:#0f2a43;">{{ $customer->full_name }}</b>,
    </p>

    <p style="
        font-size:15px;
        color:#555;
        line-height:22px;
        margin:0 0 20px;
    ">
        Your Passport Seva account has been created successfully.
        Please find your login credentials below.
    </p>


    {{-- CREDENTIALS CARD --}}
    <div
        style="
        background:#f4f8f9;
        border:1px solid #d5e4e7;
        border-radius:10px;
        padding:20px;
    ">

        <p
            style="
            font-size:14px;
            color:#0f2a43;
            line-height:20px;
            margin:0 0 15px;
            font-weight:bold;
        ">
            Passport Seva Login Details
        </p>


        {{-- USERNAME ROW --}}
        <div
            style="
            background:#ffffff;
            border:1px solid #d5e4e7;
            border-left:4px solid #0f9d8a;
            border-radius:7px;
            padding:13px 15px;
            margin-bottom:12px;
        ">

            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>

                    <td
                        style="
                        width:18%;
                        font-size:13px;
                        color:#667085;
                        font-weight:600;
                        vertical-align:middle;
                    ">
                        Username :
                    </td>

                    <td
                        style="
                        font-size:15px;
                        color:#0f2a43;
                        font-weight:bold;
                        vertical-align:middle;
                        word-break:break-all;
                    ">
                        {{ $username }}
                    </td>

                </tr>
            </table>

        </div>


        {{-- PASSWORD ROW --}}
        <div
            style="
            background:#ffffff;
            border:1px solid #d5e4e7;
            border-left:4px solid #0f9d8a;
            border-radius:7px;
            padding:13px 15px;
            margin-bottom:12px;
        ">

            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>

                    <td
                        style="
                        width:18%;
                        font-size:13px;
                        color:#667085;
                        font-weight:600;
                        vertical-align:middle;
                    ">
                        Password :
                    </td>

                    <td
                        style="
                        font-size:15px;
                        color:#0f2a43;
                        font-weight:bold;
                        vertical-align:middle;
                        word-break:break-all;
                    ">
                        {{ $password }}
                    </td>

                </tr>
            </table>

        </div>

        {{-- URL ROW --}}
        <div
            style="
            background:#ffffff;
            border:1px solid #d5e4e7;
            border-left:4px solid #0f9d8a;
            border-radius:7px;
            padding:13px 15px;
        ">

            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>

                    <td
                        style="
                        width:18%;
                        font-size:13px;
                        color:#667085;
                        font-weight:600;
                        vertical-align:middle;
                    ">
                        Login url :
                    </td>

                    <td
                        style="
                        font-size:15px;
                        color:#0f2a43;
                        font-weight:bold;
                        vertical-align:middle;
                        word-break:break-all;
                    ">
                        {{ $login_short_url }}
                    </td>

                </tr>
            </table>

        </div>

    </div>


    {{-- SECURITY NOTICE --}}
    <div
        style="
        margin-top:20px;
        background:#fff8e1;
        border:1px solid #f5d67a;
        border-radius:8px;
        padding:14px 16px;
    ">
        <p
            style="
            margin:0;
            font-size:13px;
            color:#8a5a00;
            line-height:20px;
        ">
            <strong>Security Notice:</strong>
            Please keep your Passport Seva login credentials secure
            and do not share them with anyone.
        </p>
    </div>


    <p style="
        margin:22px 0 0;
        font-size:14px;
        color:#555;
        line-height:22px;
    ">
        Thank you,<br>
        <b style="color:#0f2a43;">
            Passport Suvidha Team
        </b>
    </p>
@endsection
