<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Tax Invoice - Passport Suvidha Service</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family:
                DejaVu Sans,
                sans-serif;
            font-size: 11px;
            color: #111;
            background: #fff;
            margin: 30px;
            margin-bottom: 220px;
            padding: 0px;
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
            /* border: 1px solid #222; */
        }

        .border td,
        .border th {
            /* border: 0.6px solid #999; */
        }

        /* .border {
            width: 100%;
            border: 1px solid #222;
            table-layout: fixed;
        }

        .border tr td:last-child,
        .border tr th:last-child {
            border-right: 1px solid #999 !important;
        } */

        td,
        th {
            padding: 10px 12px;
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

        .company-name {
            font-size: 26px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 1px solid #222;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .section-heading {
            background: #f5f5f5;
            /* border: 1px solid #bbb; */
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
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        tr td {
            background: #fff;
        }

        tr.bold td {
            background: #f8f8f8;
            font-weight: 700;
            border-top: 1px solid #222;
        }

        .grand-total-box {
            background: #fafafa !important;
            font-size: 14px;
            font-weight: 700;
            padding: 10px;
        }

        .amount-words {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 12px;
            line-height: 22px;
            letter-spacing: 0.3px;
        }

        .payment-section {
            background: #fafafa;
        }

        ol {
            margin: 8px 0 0 18px !important;
            padding: 0;
        }

        ol li {
            margin-bottom: 6px;
        }

        strong {
            font-weight: 700;
        }

        .border tr:first-child td {
            padding-top: 14px;
            padding-bottom: 14px;
        }

        @page {
            margin: 12px;
        }

        .border tr td:last-child,
        .border tr th:last-child {
            border-right: 1px solid #cfcfcf !important;
        }

        .border tr td:first-child,
        .border tr th:first-child {
            border-left: 1px solid #cfcfcf !important;
        }




        .border td,
        .border th {
            border: 1px solid #cfcfcf;
            padding: 10px;
            vertical-align: top;
        }
    </style>
</head>

<body>
    <table class="border" cellspacing="0" cellpadding="0" style="margin: 0 auto">
        <tr>
            <td colspan="6" class="company-header">
                <h1>Passport Suvidha Service</h1>
            </td>
        </tr>
        <tr class="header-info">
            <td colspan="3" class="left">
                Second Floor, Shop No. 227<br />
                Unique Square, Opp. Shubham K Mart<br />
                Singanpore Road, Surat - 395004<br />
            </td>
            <td colspan="3" class="right">
                <strong>Mobile :</strong> +91 7486046591<br />
                <strong>Email :</strong> support@passportsuvidha.com<br />
                <strong>GSTIN :</strong> 24AAHFU1938J1ZZ
            </td>
        </tr>

        <tr>
            <td colspan="5">
                <div class="section-heading">Bill To</div>

                <strong>Name :</strong>
                {{ $customer->first_name ?? '' }} {{ $customer->last_name ?? '' }}
                <br />

                <strong>Mobile :</strong>
                {{ $customer->mobile_number ?? '' }}
                <br />

                <strong>Email :</strong>
                {{ $customer->email ?? '' }}
                <br />

                <strong>City :</strong>
                {{ $customer->city ?? '' }}
            </td>

            <td colspan="4" class="header-info">
                <div class="section-heading">Invoice</div>
                <strong>Invoice No :</strong>
                {{ $invoice->id ?? 'N/A' }}
                <br />
                <strong>Invoice Date :</strong>
                {{ $invoice->created_at ? \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y') : date('d-m-Y') }}
                <br />
                <strong>Payment ID :</strong>
                {{ $payment_id ?? 'N/A' }}
                {{-- <br /><br /> --}}
            </td>
        </tr>

        <tr class="bold center">
            <td width="5%">Sr.</td>

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

        <tr>
            <td class="center">1</td>
            {{-- <td>Government Passport Fee</td> --}}
            <td>Government Passport Fee: <br>{{ $service->service_name ?? 'Passport Assistance Service' }}</td>
            <td class="right">₹{{ number_format($gov_amount, 2) }}</td>
            <td class="right">0%</td>
            <td class="center">-</td>
            <td class="right">0.00</td>
            <td class="right">₹{{ number_format($gov_amount, 2) }}</td>
        </tr>

        @if (strtolower(trim($is_gujarat)))
            <tr>
                <td class="center">2</td>

                <td>Service Charges</td>


                <td class="right">₹{{ number_format($service_charges, 2) }}</td>

                <td class="right">{{ $gst_rate / 2 }}%</td>

                <td class="center">CGST</td>
                <td class="right">₹{{ number_format($cgst, 2) }}</td>

                <td class="right">
                    ₹{{ number_format($service_charges + $cgst, 2) }}
                </td>
            </tr>
            <tr>
                <td class="center"></td>

                <td></td>

                {{-- <td class="center">998599</td> --}}

                <td class="right"></td>

                <td class="right">{{ $gst_rate / 2 }}%</td>

                <td class="center">SGST</td>
                <td class="right">₹{{ number_format($sgst, 2) }}</td>

                <td class="right">
                    ₹{{ number_format($sgst + $igst, 2) }}
                </td>
            </tr>
        @else
            <tr>
                <td class="center">2</td>

                <td>Service Charges</td>

                {{-- <td class="center">998599</td> --}}

                <td class="right">₹{{ number_format($service_charges, 2) }}</td>

                <td class="right">{{ $gst_rate }}%</td>

                <td class="center">IGST</td>
                <td class="right">₹{{ number_format($cgst + $sgst + $igst, 2) }}</td>

                <td class="right">
                    ₹{{ number_format($service_charges + $cgst + $sgst + $igst, 2) }}
                </td>
            </tr>
        @endif
        <tr class="bold">
            <td colspan="2" class="right">TOTAL</td>

            <td class="right">
                ₹{{ number_format($gov_amount + $service_charges, 2) }}
            </td>
            <td></td>
            <td></td>

            <td class="right">₹{{ number_format($cgst + $sgst + $igst, 2) }}</td>

            <td class="right">
                ₹{{ number_format($gov_amount + $service_charges + $cgst + $sgst + $igst, 2) }}
            </td>
        </tr>
        {{--
        <tr>
            <td colspan="9">
                <strong>Total Amount In Words</strong>
                <br /><br />
 
                <span class="amount-words">
                    {{ strtoupper($amount_in_words ?? 'RUPEES ONLY') }}
        </span>
        </td>

        <td colspan="4">
            <table class="grand-total-box" style="width: 100%; border-collapse: collapse">
                <tr>
                    <td class="bold">Grand Total</td>
                    <td class="right bold">₹{{ number_format($grand_total, 2) }}</td>
                </tr>
            </table>
        </td>
        </tr> --}}

        <tr>
            <td colspan="9">
                <strong class="section-heading">Payment Information</strong>
                <br /><br />

                Payment Mode : {{ $payment_mode ?? 'Online' }}

                <br />

                Payment ID : {{ $payment_id ?? 'N/A' }}
            </td>
        </tr>

        <tr>
            <td colspan="9" class="header-info" style="align-items: center; text-align: center">
                <p>Authorized Person: Passport Suvidha Service</p>
                <p>This is a computer-generated invoice. No signature
                    required.</p>
            </td>
        </tr>
    </table>
</body>

</html>
