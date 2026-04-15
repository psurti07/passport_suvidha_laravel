<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Invoice - Passport Suvidha Service</title>

<style>
body {
    font-family: DejaVu Sans, Arial, sans-serif;
    background: #f5f5f5;
    margin: 0;
    padding: 20px;
    color: #333;
}

.container {
    width: 800px;
    margin: auto;
    background: #fff;
    padding: 30px;
    border-radius: 8px;
}

/* HEADER */
.header {
    display: flex;
    justify-content: space-between;
    border-bottom: 2px solid #eee;
    padding-bottom: 20px;
}

.company-info h2 {
    margin: 0;
    font-size: 22px;
}

.company-info p {
    margin: 3px 0;
    font-size: 12px;
}

.invoice-box {
    text-align: right;
}

.invoice-box h1 {
    margin: 0;
    font-size: 28px;
}

.invoice-box p {
    margin: 5px 0;
    font-size: 13px;
}

/* BILL TO */
.bill-to {
    margin-top: 20px;
    padding: 10px;
    background: #fafafa;
    border-radius: 6px;
}

.bill-to strong {
    display: block;
    margin-bottom: 5px;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 25px;
}

th {
    background: #333;
    color: #fff;
    padding: 12px;
    text-align: left;
}

td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

/* TOTAL */
.summary {
    width: 300px;
    margin-left: auto;
    margin-top: 20px;
}

.summary p {
    display: flex;
    justify-content: space-between;
    margin: 5px 0;
}

.summary .grand {
    font-size: 16px;
    font-weight: bold;
}

/* PAYMENT */
.payment-info {
    margin-top: 25px;
    font-size: 13px;
}

/* FOOTER */
.footer {
    margin-top: 40px;
    text-align: center;
    font-size: 11px;
    color: #777;
}
</style>
</head>

<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">
        <div class="company-info">
            <h2>Passport Suvidha Service</h2>
            <p>Second Floor, Shop No. 227</p>
            <p>Unique Square, Opp. Shubham K Mart</p>
            <p>Singanpore Road, Surat - 395004</p>
            <p>+91 7486046591</p>
            <p>support@passportsuvidha.com</p>
            <p>GSTN: 24AAHFU1938J1ZZ</p>
        </div>

        <div class="invoice-box">
            <h1>Invoice</h1>
            <p><strong>No:</strong> {{ $invoice->id ?? 'N/A' }}</p>
            <p><strong>Date:</strong> 
                {{ $invoice->created_at ? \Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y') : now()->format('d/m/Y') }}
            </p>
        </div>
    </div>

    <!-- BILL TO -->
    <div class="bill-to">
        <strong>Bill To:</strong>
        {{ $customer->first_name ?? '' }} {{ $customer->last_name ?? '' }}<br>
        {{ $customer->city ?? '' }}<br>
        (M) {{ $customer->mobile_number ?? '' }}<br>
        (E) {{ $customer->email ?? '' }}
    </div>

    <!-- TABLE -->
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th width="80">Qty</th>
                <th width="150">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @if ($service)
            <tr>
                <td>{{ $service->service_name ?? 'Service' }}</td>
                <td>1</td>
                <td>{{ number_format($payment_amount, 2) }}</td>
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
    <div class="summary">
        <p><span>Subtotal:</span><span>₹{{ number_format($payment_amount, 2) }}</span></p>
        <p class="grand"><span>Total:</span><span>₹{{ number_format($payment_amount, 2) }}</span></p>
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