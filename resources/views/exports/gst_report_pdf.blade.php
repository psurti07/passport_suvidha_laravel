<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>GST Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        h1 {
            font-size: 18px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>GST Report</h1>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>INV Date</th>
                <th>INV #</th>
                <th>Net Amount</th>
                <th>CGST</th>
                <th>SGST</th>
                <th>IGST</th>
                <th>Total Amount</th>
                <th>Fullname</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>GST No</th>
                <th>City</th>
                <th>State</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->inv_date ? \Carbon\Carbon::parse($item->inv_date)->format('d/m/Y') : '' }}</td>
                    <td>{{ $item->inv_no ?? '' }}</td>
                    <td>{{ number_format($item->net_amount ?? 0, 2) }}</td>
                    <td>{{ number_format($item->cgst ?? 0, 2) }}</td>
                    <td>{{ number_format($item->sgst ?? 0, 2) }}</td>
                    <td>{{ number_format($item->igst ?? 0, 2) }}</td>
                    <td>{{ number_format($item->total_amount ?? 0, 2) }}</td>
                    <td>{{ $item->fullname ?? '' }}</td>
                    <td>{{ $item->mobile ?? '' }}</td>
                    <td>{{ strtolower($item->email ?? '') }}</td>
                    <td>{{ $item->gst_no ?? '' }}</td>
                    <td>{{ $item->city ?? '' }}</td>
                    <td>{{ $item->state ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" style="text-align: center; padding: 20px;">No data available for the selected criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
