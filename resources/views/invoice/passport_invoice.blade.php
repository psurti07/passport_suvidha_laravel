<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice - Passport Suvidha Service</title>

    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 95%;
            padding: 2.5%;
        }

        /* HEADER TABLE (DomPDF Safe) */
        .header-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .company-info {
            width: 65%;
            vertical-align: top;
        }

        .invoice-info {
            width: 35%;
            text-align: right;
            vertical-align: top;
        }

        .company-info h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
        }

        .company-info p {
            margin: 2px 0;
        }

        /* BILL TO */
        .bill-to {
            margin-top: 10px;
        }

        .bill-to strong {
            display: block;
            margin-bottom: 3px;
        }

        /* INVOICE TITLE */
        .invoice-title {
            font-size: 22px;
            margin: 0 0 10px 0;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background: #333;
            color: #fff;
            padding: 10px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        /* TOTAL */
        .total-section {
            width: 100%;
            margin-top: 10px;
        }

        .total-table {
            width: 300px;
            margin-left: auto;
        }

        .total-table td {
            border: none;
            padding: 5px 0;
        }

        .total-table .grand {
            border-top: 1px solid #ddd;
            font-weight: bold;
            font-size: 14px;
        }

        /* PAYMENT */
        .payment-info {
            margin-top: 15px;
        }

        .payment-info p {
            margin: 3px 0;
        }

        /* FOOTER */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td class="company-info">
                    <h2>Passport Suvidha Service</h2>
                    <p>Second Floor, Shop No. 227</p>
                    <p>Unique Square, Opp. Shubham K Mart</p>
                    <p>Singanpore Road, Surat - 395004</p>
                    <p>+91 7486046591</p>
                    <p>support@passportsuvidha.com</p>
                    <p>GSTN: 24AAHFU1938J1ZZ</p>

                    <div class="bill-to">
                        <strong>Bill To:</strong>
                        {{ $customer->first_name ?? '' }} {{ $customer->last_name ?? '' }}<br>
                        {{ $customer->city ?? '' }}<br>
                        (M) {{ $customer->mobile_number ?? '' }}<br>
                        (E) {{ $customer->email ?? '' }}
                    </div>
                </td>

                <td class="invoice-info">
                    <h1 class="invoice-title">Invoice</h1>
                    <p><strong>Invoice No:</strong> {{ $invoice->id ?? 'N/A' }}</p>
                    <p><strong>Date:</strong>
                        {{ $invoice->created_at ? \Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y') : now()->format('d/m/Y') }}
                    </p>
                </td>
            </tr>
        </table>

        <!-- ITEMS TABLE -->
        <table>
            <thead>
                <tr>
                    <th align="left">Item</th>
                    <th width="80" align="right">Qty</th>
                    <th width="150" align="right">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                @if ($service)
                    <tr>
                        <td>{{ $service->service_name ?? 'Service' }}</td>
                        <td align="right">1</td>
                        <td align="right">{{ number_format($payment_amount, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td>No Service Found</td>
                        <td>-</td>
                        <td>0.00</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- TOTAL -->
        <div class="total-section">
            <table class="total-table">

                <tr>
                    <td>Government Charges:</td>
                    <td align="right">₹{{ number_format($gov_amount, 2) }}</td>
                </tr>

                <tr>
                    <td>Service Charges:</td>
                    <td align="right">₹{{ number_format($service_charges, 2) }}</td>
                </tr>

                @if ($is_gujarat)
                    <tr>
                        <td>CGST ({{ $gst_rate / 2 }}%):</td>
                        <td align="right">₹{{ number_format($cgst, 2) }}</td>
                    </tr>
                    <tr>
                        <td>SGST ({{ $gst_rate / 2 }}%):</td>
                        <td align="right">₹{{ number_format($sgst, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td>IGST ({{ $gst_rate }}%):</td>
                        <td align="right">₹{{ number_format($igst, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td></td>
                    <td></td>
                </tr>

                <tr class="grand">
                    <td><strong>Grand Total:</strong></td>
                    <td align="right">
                        <strong>₹{{ number_format($grand_total, 2) }}</strong>
                    </td>
                </tr>

            </table>
        </div>

        <!-- PAYMENT -->
        <div class="payment-info">
            <p><strong>Payment Method:</strong> {{ $payment_mode }}</p>
            <p><strong>Payment ID:</strong> {{ $payment_id }}</p>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p>Authorized Person: {{ $invoice->authorized_person ?? 'Passport Suvidha Service' }}</p>
            <p>This is a computer-generated invoice. No signature required.</p>
        </div>

    </div>

</body>

</html>
