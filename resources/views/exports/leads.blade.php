<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Leads Export</title>
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
    <h1>Leads Report</h1>
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
            @forelse($leads as $lead)
                <tr>
                    <td>{{ $lead->id }}</td>
                    <td>{{ $lead->first_name }} {{ $lead->last_name }}</td>
                    <td>{{ $lead->email }}</td>
                    <td>{{ $lead->mobile_number }}</td>
                    <td class="{{ $lead->is_paid ? 'status-paid' : 'status-lead' }}">
                        {{ $lead->is_paid ? 'Paid' : 'Lead' }}
                    </td>
                    <td>{{ $lead->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $lead->pack_code }}</td>
                    <td>{{ $lead->address }}</td>
                    <td>{{ $lead->gender }}</td>
                    <td>{{ $lead->date_of_birth ? date('Y-m-d', strtotime($lead->date_of_birth)) : '' }}</td>
                    <td>{{ $lead->place_of_birth }}</td>
                    <td>{{ $lead->nationality }}</td>
                    <td>{{ $lead->service_code }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" style="text-align: center;">No leads found for the selected criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html> 