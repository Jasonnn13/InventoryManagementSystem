<!DOCTYPE html>
<html>
<head>
    <title>Overdue penjualans</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
        tr {
            background-color: red;
        }
    </style>
</head>
<body>
    <p>Dear User,</p>
    <p>The penjualan for the following purchases is overdue. Please take action immediately.</p>

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

    <p>Customer Contact : {{$penjualan->customer->contact_information}}</p>
    <p>Customer Address : {{$penjualan->customer->address}}</p>
    <p>Thank You !</p>
</body>
</html>
