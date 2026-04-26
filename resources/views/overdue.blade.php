<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overdue Payment Notice</title>
    <style>
        body {
            margin: 0;
            padding: 32px 16px;
            background: #f5f5f5;
            color: #111;
            font-family: Arial, sans-serif;
        }

        .sheet {
            max-width: 760px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #111;
            padding: 24px;
        }

        h1 {
            margin-top: 0;
            font-size: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0;
        }

        th, td {
            border: 1px solid #111;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #efefef;
        }

        .notice {
            padding: 12px 14px;
            border-left: 4px solid #111;
            background: #fafafa;
            margin: 18px 0;
        }
    </style>
</head>
<body>
    <div class="sheet">
        <h1>Overdue Payment Notification</h1>
        <p>Dear User,</p>
        <p>The following sales record is overdue. Please take action immediately.</p>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Total Netto</th>
                    <th>Status</th>
                    <th>Tipe</th>
                    <th>Sales</th>
                    <th>Tenggat Waktu</th>
                    <th>Created By</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $penjualan->customer->name }}</td>
                    <td>Rp. {{ number_format($penjualan->total_netto, 0, ',', '.') }}</td>
                    <td>{{ $penjualan->status }}</td>
                    <td>{{ $penjualan->tipe }}</td>
                    <td>{{ $penjualan->sales }}</td>
                    <td>{{ $penjualan->tenggat_waktu->format('d-m-Y') }}</td>
                    <td>{{ $penjualan->user->name }}</td>
                    <td>{{ $penjualan->created_at->format('d-m-Y') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="notice">
            <div>Customer Contact: {{ $penjualan->customer->contact_information }}</div>
            <div>Customer Address: {{ $penjualan->customer->address }}</div>
        </div>

        <p>Thank you.</p>
    </div>
</body>
</html>
