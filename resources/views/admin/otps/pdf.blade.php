<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>OTP Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .verified {
            color: green;
        }
        .pending {
            color: orange;
        }
    </style>
</head>
<body>
    <h2>OTP Report</h2>
    <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Mobile Number</th>
                <th>OTP</th>
                <th>Sent At</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($otps as $otp)
            <tr>
                <td>{{ $otp->id }}</td>
                <td>{{ $otp->mobile_number }}</td>
                <td>{{ $otp->otp }}</td>
                <td>{{ $otp->sent_at->format('Y-m-d H:i:s') }}</td>
                <td class="{{ $otp->is_verified ? 'verified' : 'pending' }}">
                    {{ $otp->is_verified ? 'Verified' : 'Pending' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No OTPs found for the selected criteria.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html> 