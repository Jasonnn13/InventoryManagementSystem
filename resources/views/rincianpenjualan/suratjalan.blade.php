<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $penjualan->id }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: blue;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 10px;
            border: 1px solid #000;
            background-color: #fff;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .left-side {
            width: 48%;
            text-align: left;
        }
        .right-side {
            width: 48%;
            text-align: right;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0;
            font-size: 12px;
        }
        .details h2 {
            text-align: center;
            font-size: 16px;
            margin: 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #000;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .details th, .details td {
            padding: 6px;
            border: 1px solid #000;
            text-align: left;
        }
        .details th {
            background-color: #f0f0f0;
            text-align: center;
        }

        .signature-section th {
            background-color: #f0f0f0;
            text-align: left;
        }
        .totals {
            margin-top: 20px;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals th, .totals td {
            padding: 6px;
            border: 1px solid #000;
            text-align: left;
        }
        .signature-section {
            margin-top: 20px;
        }
        .signature-section table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }
        .signature-section th, .signature-section td {
            padding: 8px;
            border: 1px solid #000;
            text-align: center;
        }
        .signature-section .empty {
            height: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="left-side">
                <h1>Wenny Plastic</h1>
                <p>Plastic Food Packaging Manufacturer</p>
                <p>Pergudangan Margomulyo Indah Blok B - 7, Surabaya</p>
            </div>
            <div class="right-side">
                <p><strong>No Delivery Order:</strong> {{ $penjualan->id }}</p>
                <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
                <p><strong>Customer:</strong> {{ $penjualan->customer->name }}</p>
                <p><strong>Address:</strong> {{ $penjualan->customer->address }}</p>
            </div>
        </div>

        <div class="details">
            <h2>Delivery Order</h2>
            <table>
                <tr>
                    <th>No</th>
                    <th>Kode Item</th>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Keterangan</th>
                </tr>
                @foreach($penjualan->rincianpenjualans as $index => $rincian)
                <tr>
                    <td style="text-align:center;">{{ $index + 1 }}.</td>
                    <td style="text-align:center;">{{ $rincian->stock->kode }}</td>
                    <td style="text-align:center;">{{ $rincian->stock->name }}</td>
                    <td style="text-align:center;">{{ $rincian->quantity }}</td>
                    <td style="text-align:center;">CTN</td>
                    <td></td>
                </tr>
                @endforeach
                <tr>
                    <th colspan="3" class="total-title" style="text-align:right;">Total QTY</th>
                    <td style="text-align:center;">{{ $penjualan->rincianpenjualans->sum('quantity') }}</td>
                    <td style="text-align:center;">CTN</td>
                    <td></td>
                </tr>
            </table>
        </div>

        <div class="signature-section">
            <table>
                <tr>
                    <th>Dibuat Oleh</th>
                    <th>Disetujui</th>
                    <th>Disetujui</th>
                    <th>Disetujui</th>
                    <th>Diterima dengan baik</th>
                </tr>
                <tr class="empty">
                    <td>{{ $penjualan->user->name }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>Administrasi</th>
                    <th>Gudang</th>
                    <th>Driver</th>
                    <th>Security</th>
                    <th>Customer</th>
                </tr>
                <tr>
                    <td style="text-align: left;">Nama : <br> Tanggal : <br> Jam :</td>
                    <td style="text-align: left;">Nama : <br> Tanggal : <br> Jam :</td>
                    <td style="text-align: left;">Nama : <br> Tanggal : <br> Jam :</td>
                    <td style="text-align: left;">Nama : <br> Tanggal : <br> Jam :</td>
                    <td style="text-align: left;">Nama : <br> Tanggal : <br> Jam :</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
