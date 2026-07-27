@extends('emails.layouts.master')

@section('content')

<h2 style="
    margin:0 0 20px;
    font-size:24px;
    color:#003366;">
    Application Status Update
</h2>

<p style="
    font-size:15px;
    color:#555;">
    Dear <b> {{ $customer->full_name }}</b>
</p>

<p style="
    font-size:15px;
    color:#555;">
    Your passport application status has been updated.
</p>

@php
$statusStyle = getStatusColor($status->slug ?? '');
@endphp
<div style="
    background:#f7f9fc;
    border:1px solid #e3e8ef;
    border-radius:10px;">

    <table width="100%"
        cellpadding="12"
        cellspacing="0"
        border="0"
        class="mobile-block"
        style="
        border-collapse:collapse;
        font-size:13px;">

        <tr>
            <td class="mobile-label" width="120">
                <strong>Status</strong>
            </td>
            <td class="mobile-value">
                <span style="
                    background:{{ $statusStyle['bg'] }};
                    color:{{ $statusStyle['text'] }};
                    padding:6px 12px;
                    border-radius:20px;
                    display:inline-block;
                    font-weight:600;">
                    {{ $status->status_name }}
                </span>
            </td>
        </tr>

        <tr>
            <td class="mobile-label">
                <strong>Status Date</strong>
            </td>
            <td class="mobile-value">
                {{ date('d M Y', strtotime($status_date)) }}
            </td>
        </tr>

        <tr>
            <td class="mobile-label">
                <strong>Remark</strong>
            </td>
            <td class="mobile-value"
                style="
                line-height:20px;
                color:#555555;
                word-break:break-word;">
                {{ $remark }}
            </td>
        </tr>

        @if($file)
        <tr>
            <td class="mobile-label">
                <strong>Attachment</strong>
            </td>
            <td class="mobile-value">
                Your document is attached with this email.
            </td>
        </tr>
        @endif

    </table>
</div>

<br>
<p style="
    font-size:14px;
    color:#555;
    line-height:22px;">
    Thank you,<br>
    <b>Passport Suvidha Team</b>
</p>

@endsection