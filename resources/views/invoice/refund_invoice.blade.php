<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Credit Note - Passport Suvidha Service</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
            background: #fff;
            margin: 30px;
            padding: 0;
            line-height: 1.6;
            border: 1px solid #222;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .border {
            width: 100%;
        }

        .border td,
        .border th {
            border: 1px solid #cfcfcf;
            padding: 10px;
            vertical-align: top;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
            white-space: nowrap;
        }

        .bold {
            font-weight: bold;
        }

        .company-header {
            padding: 0 !important;
            vertical-align: middle !important;
        }

        .company-header h1 {
            font-size: 26px;
            font-weight: 700;
            margin: 0;
            padding: 18px 10px 8px 10px;
            text-align: left;
        }

        .company-header div {
            padding: 0px 10px 2px 10px;
        }

        .section-heading {
            background: #f5f5f5;
            padding: 7px 10px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .header-info {
            background: #fafafa;
        }

        tr.bold.center td,
        tr.bold.center th {
            background: #f3f3f3;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        tr.bold td {
            background: #f8f8f8;
            font-weight: 700;
            border-top: 1px solid #222;
        }

        .refund-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            padding: 12px;
            background: #f5f5f5;
            letter-spacing: 1px;
        }

        .refund-status {
            font-weight: bold;
            text-transform: uppercase;
        }

        .payment-section {
            background: #fafafa;
        }

        strong {
            font-weight: 700;
        }

        @page {
            margin: 12px;
        }
    </style>
</head>

<body>

    <table class="border" cellspacing="0" cellpadding="0">

        {{-- COMPANY HEADER --}}
        <tr>
            <td colspan="7" class="company-header">
                <h1>Passport Suvidha Service</h1>

                <div class="document-title">
                    CREDIT NOTE / REFUND RECEIPT
                </div>
            </td>
        </tr>

        {{-- COMPANY INFORMATION --}}
        <tr class="header-info">

            <td colspan="4">
                Second Floor, Shop No. 227<br />
                Unique Square, Opp. Shubham K Mart<br />
                Singanpore Road, Surat - 395004
            </td>

            <td colspan="3">
                <strong>Mobile :</strong> +91 6358292349<br />
                <strong>Email :</strong> support@passportsuvidha.com<br />
                <strong>GSTIN :</strong> 24ABEFB9441P1Z1
            </td>

        </tr>

        <tr>

            {{-- BILL TO --}}
            <td colspan="4">

                <div class="section-heading">
                    Bill To
                </div>

                <strong>Name :</strong>
                {{ $customer->full_name ?? 'N/A' }}
                <br />

                <strong>Mobile :</strong>
                {{ $customer->mobile_number ?? 'N/A' }}
                <br />

                <strong>Email :</strong>
                {{ $customer->email ?? 'N/A' }}
                <br />

                <strong>City :</strong>
                {{ $customer->city ?? 'N/A' }}

            </td>

            {{-- REFUND DETAILS --}}
            <td colspan="3" class="header-info">

                <div class="section-heading">
                    Refund Details
                </div>

                <strong>Refund No :</strong><br />
                {{ $refund->refund_no ?? 'N/A' }}
                <br />

                <strong>Refund Date :</strong>
                {{ $refund->refunded_at ? \Carbon\Carbon::parse($refund->refunded_at)->format('d-m-Y') : date('d-m-Y') }}
                <br />

                <strong>Original Invoice :</strong>
                {{ $invoice->inv_no ?? 'N/A' }}
                <br />

                <strong>Invoice Date :</strong>
                {{ $invoice->created_at ? \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y') : 'N/A' }}
                <br />

                <strong>Payment ID :</strong>
                {{ $refund->payment_id ?? 'N/A' }}

            </td>

        </tr>

        {{-- REFUND TABLE HEADER --}}
        <tr class="bold center">

            <td width="5%">
                Sr.
            </td>

            <td width="33%" align="left">
                Particulars
            </td>

            <td width="13%" align="right">
                Amount
            </td>

            <td width="10%" align="right">
                GST %
            </td>

            <td width="12%" align="center">
                GST Type
            </td>

            <td width="12%" align="right">
                GST Amt
            </td>

            <td width="15%" align="right">
                Total
            </td>

        </tr>

        {{-- REFUND ITEM --}}
        <tr>

            <td class="center">
                1
            </td>

            <td>
                Refund against Service Charges
                <br>

                <small>
                    Original Invoice:
                    {{ $invoice->inv_no ?? 'N/A' }}
                </small>
            </td>

            <td class="right">
                ₹{{ number_format($net_amount ?? 0, 2) }}
            </td>

            <td class="right">
                {{ $gst_rate ?? 0 }}%
            </td>

            <td class="center">

                @if (strtolower(trim($is_gujarat ?? '')))
                    CGST / SGST
                @else
                    IGST
                @endif

            </td>

            <td class="right">
                ₹{{ number_format(($cgst ?? 0) + ($sgst ?? 0) + ($igst ?? 0), 2) }}
            </td>

            <td class="right">
                ₹{{ number_format($refund->amount ?? 0, 2) }}
            </td>

        </tr>

        {{-- TOTAL --}}
        <tr class="bold">

            <td colspan="2" class="right">
                REFUND TOTAL
            </td>

            <td class="right">
                ₹{{ number_format($net_amount ?? 0, 2) }}
            </td>

            <td></td>

            <td></td>

            <td class="right">
                ₹{{ number_format(($cgst ?? 0) + ($sgst ?? 0) + ($igst ?? 0), 2) }}
            </td>

            <td class="right">
                ₹{{ number_format($refund->amount ?? 0, 2) }}
            </td>

        </tr>

        {{-- REFUND INFORMATION --}}
        <tr>

            <td colspan="7">

                <strong class="section-heading">
                    Refund Information
                </strong>

                <br /><br />

                <strong>Refund Status :</strong>

                <span class="refund-status">
                    {{ strtoupper($refund->status ?? 'PROCESSED') }}
                </span>

                <br />

                <strong>Refund Date :</strong>

                {{ $refund->refunded_at ? \Carbon\Carbon::parse($refund->refunded_at)->format('d-m-Y H:i') : date('d-m-Y H:i') }}

                <br />

                <strong>Refund Amount :</strong>

                ₹{{ number_format($refund->amount ?? 0, 2) }}

                <br />

                <strong>Reason :</strong>

                {{ $refund->remark ?? 'Application cancelled and refund processed.' }}

            </td>

        </tr>

        {{-- PAYMENT / ORIGINAL TRANSACTION --}}
        <tr>

            <td colspan="7" class="payment-section">

                <strong class="section-heading">
                    Original Payment Information
                </strong>

                <br /><br />

                Payment Mode :
                {{ $payment_mode ?? 'Online' }}

                <br />

                Original Payment ID :
                {{ $refund->payment_id ?? 'N/A' }}

                <br />

                Original Invoice No :
                {{ $invoice->inv_no ?? 'N/A' }}

            </td>

        </tr>

        {{-- FOOTER --}}
        <tr>

            <td colspan="7" class="header-info" style="text-align:center">

                <p>
                    Authorized Person: Passport Suvidha Service
                </p>

                <p>
                    This is a computer-generated credit note / refund receipt.
                    No signature required.
                </p>

            </td>

        </tr>

    </table>

</body>

</html>
