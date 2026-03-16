<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customers Export</title>
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
        .status-paid {
            color: #0f766e;
        }
        .status-lead {
            color: #dc2626;
        }
        h1 {
            font-size: 18px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Customers Report</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Pack Code</th>
                <th>Address</th>
                <th>Gender</th>
                <th>DOB</th>
                <th>POB</th>
                <th>Nationality</th>
                <th>Service Code</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->first_name }} {{ $customer->last_name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->mobile_number }}</td>
                    <td class="{{ $customer->is_paid ? 'status-paid' : 'status-lead' }}">
                        {{ $customer->is_paid ? 'Paid' : 'Lead' }}
                    </td>
                    <td>{{ $customer->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $customer->pack_code }}</td>
                    <td>{{ $customer->address }}</td>
                    <td>{{ $customer->gender }}</td>
                    <td>{{ $customer->date_of_birth ? date('Y-m-d', strtotime($customer->date_of_birth)) : '' }}</td>
                    <td>{{ $customer->place_of_birth }}</td>
                    <td>{{ $customer->nationality }}</td>
                    <td>{{ $customer->service_code }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" style="text-align: center;">No customers found for the selected criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html> 