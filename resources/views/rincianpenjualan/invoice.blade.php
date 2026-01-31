<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $penjualan->id }}</title>
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
            height: 120px; /* Ensure enough height for the header */
            margin-bottom: 10px;
            position: relative;
        }
        .left-side {
            width: 48%;
            float: left;
            text-align: left;
        }
        .right-side {
            width: 48%;
            float: right;
            text-align: right;
            position: absolute;
            top: 0;
            right: 0;
        }
        .header h1 {
            font-size: 20px;
            margin: 0;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0;
        }
        .details {
            margin-bottom: 10px;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details th, .details td {
            padding: 6px;
            border: 1px solid #000;
            text-align: left;
            white-space: nowrap;
        }
        .details th {
            background-color: #f0f0f0;
            text-align: center;
        }
        .totals {
            margin-top: 20px;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .totals th, .totals td {
            padding: 6px;
            text-align: right;
            border: 1px solid #000;
        }
        .totals .total-title {
            text-align: left;
            padding-right: 20px;
            font-weight: bold;
        }
        @media print {
            .header {
                page-break-inside: avoid;
            }
            .details {
                page-break-inside: auto;
            }
            .totals {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="left-side">
                <h1><strong>Wenny Plastik</strong></h1>
                <p>Plastic Food Packaging Distributor</p> 
                <p>Pergudangan Margomulyo Indah 17B Blok B no. 7</p>
                <p>Phone : 087855340028</p>
            </div>
            <div class="right-side" style="font-size:15px; text-align:left;">
                <p><strong>Tanggal:</strong>   {{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
                <p><strong>Sales:</strong>     {{ $penjualan->sales }}</p>
                <p><strong>Customer:</strong>  {{ $penjualan->customer->name }}</p>
                <p><strong>Address:</strong>   {{ $penjualan->customer->address }}</p>
            </div>
        </div>

        <div class="details">
            <h2 style="text-align:center;">Invoice / Titipan Barang</h2>
            <table>
                <tr>
                    <th>No</th>
                    <th>Kode Item</th>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Harga Satuan</th>
                    <th>Total</th>
                </tr>
                @foreach($penjualan->rincianpenjualans as $index => $rincian)
                <tr>
                    <td style="text-align:center;">{{ $index + 1 }}. </td>
                    <td>{{ $rincian->stock->kode }}</td>
                    <td>{{ $rincian->stock->name }}</td>
                    <td>{{ $rincian->quantity }}</td>
                    <td>CTN</td>
                    <td>Rp. {{ number_format($rincian->price, 0, ',', '.') }}</td>
                    <td>Rp. {{ number_format($rincian->total, 0, ',', '.') }}</td>
                </tr>   
                @endforeach
                <tr>
                    <th colspan="3" class="total-title" style="text-align:right;">Total QTY</th>
                    <td>{{ $penjualan->rincianpenjualans->sum('quantity') }}</td>
                    <td>CTN</td>
                    <th>Total</th>
                    <td>Rp. {{ number_format($penjualan->total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="5" rowspan="4" class="total-title">
                        <p style="font-size:13px;">Tanggal Jatuh Tempo: {{ $penjualan->tenggat_waktu->format('d-m-Y') }}<br>
                        Keterangan : Pembayaran dapat Ditransfer ke <br>
                        1. BCA a/c 8290727597 Diatasnamakan Agus Widodo Purbadi<br>
                        2. Untuk Bilyet Giro atau cek ke BCA a/c 8290727597 Diatasnamakan Agus Widodo Purbadi<br>
                        3. Maksimal Retur Barang dan Komplain Harga 7 Hari Kerja terhitung dari <br> tanggal dikeluarkannya Invoice</p>
                    </td>
                    <th class="total-title">Diskon</th>
                    <td>Rp. {{ number_format(($penjualan->diskon / 100) * $penjualan->total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th class="total-title">DPP</th>
                    <td>Rp. {{ number_format($penjualan->dpp, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th class="total-title">PPN</th>
                    <td>Rp. {{ number_format($penjualan->ppn, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th class="total-title">Total Netto</th>
                    <td>Rp. {{ number_format($penjualan->total_netto, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th colspan="2" class="total-title">Administrasi</th>
                    <th colspan="3" class="total-title">Accounting</th>
                    <th colspan="2" class="total-title">Finance</th>
                </tr>
                <tr style="height: 100px;"> <!-- Adjusted height -->
                    <td colspan="2" class="total-title"><br><br><br></td>
                    <td colspan="3" class="total-title"><br><br><br></td>
                    <td colspan="2" class="total-title"><br><br><br></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
