<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $penjualan->id }}</title>
    <style>
        body {
            margin: 0;
            padding: 24px;
            background: #f5f5f5;
            color: #111;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .sheet {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #111;
            padding: 18px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 16px;
        }

        .header h1 {
            margin: 0 0 6px;
            font-size: 20px;
        }

        .header p {
            margin: 2px 0;
        }

        h2 {
            margin: 16px 0 12px;
            text-align: center;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #111;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #efefef;
        }

        .right {
            text-align: right;
        }

        .signature-row td {
            height: 90px;
        }

        .signature-labels th {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="header">
            <div>
                <h1>CV Haathee Plastik</h1>
                <p>Plastic Food Packaging Manufacturer</p>
                <p>Pergudangan Margomulyo Indah Blok B - 7, Surabaya</p>
            </div>
            <div class="right">
                <p><strong>No Delivery Order:</strong> {{ $penjualan->id }}</p>
                <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
                <p><strong>Customer:</strong> {{ $penjualan->customer->name }}</p>
                <p><strong>Address:</strong> {{ $penjualan->customer->address }}</p>
            </div>
        </div>

        <h2>Delivery Order</h2>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Item</th>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualan->rincianpenjualans as $index => $rincian)
                    <tr>
                        <td class="right">{{ $index + 1 }}</td>
                        <td>{{ $rincian->stock->kode }}</td>
                        <td>{{ $rincian->stock->name }}</td>
                        <td>{{ $rincian->quantity }}</td>
                        <td>CTN</td>
                        <td></td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="3" class="right">Total QTY</th>
                    <td>{{ $penjualan->rincianpenjualans->sum('quantity') }}</td>
                    <td>CTN</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <table style="margin-top: 20px;">
            <thead class="signature-labels">
                <tr>
                    <th>Dibuat Oleh</th>
                    <th>Disetujui</th>
                    <th>Disetujui</th>
                    <th>Disetujui</th>
                    <th>Diterima dengan baik</th>
                </tr>
            </thead>
            <tbody>
                <tr class="signature-row">
                    <td>{{ $penjualan->user->name }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="signature-labels">
                    <th>Administrasi</th>
                    <th>Gudang</th>
                    <th>Driver</th>
                    <th>Security</th>
                    <th>Customer</th>
                </tr>
                <tr>
                    <td>Nama:<br>Tanggal:<br>Jam:</td>
                    <td>Nama:<br>Tanggal:<br>Jam:</td>
                    <td>Nama:<br>Tanggal:<br>Jam:</td>
                    <td>Nama:<br>Tanggal:<br>Jam:</td>
                    <td>Nama:<br>Tanggal:<br>Jam:</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
