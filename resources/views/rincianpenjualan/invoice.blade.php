<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $penjualan->id }}</title>
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
    </style>
</head>
<body>
    @php
        $isPpnInvoice = ($penjualan->tipe_ppn ?? 'Non PPN') === 'PPN';
        $invoiceTotalNetto = (float) $penjualan->total_netto;
        $invoiceDpp = $isPpnInvoice ? ($invoiceTotalNetto / 1.11) : $invoiceTotalNetto;
        $invoicePpn = $isPpnInvoice ? ($invoiceTotalNetto - $invoiceDpp) : 0;
    @endphp
    <div class="sheet">
        <div class="header">
            <div>
                <h1>CV Haathee Plastik</h1>
                <p>Plastic Food Packaging Distributor</p>
                <p>Pergudangan Margomulyo Indah 17B Blok B no. 7</p>
                <p>Phone: 087855340028</p>
            </div>
            <div class="right">
                <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
                <p><strong>Sales:</strong> {{ $penjualan->sales }}</p>
                <p><strong>Tipe:</strong> {{ $penjualan->tipe }}</p>
                <p><strong>Invoice:</strong> {{ $penjualan->tipe_ppn ?? 'Non PPN' }}</p>
                <p><strong>Customer:</strong> {{ $penjualan->customer->name }}</p>
                <p><strong>Address:</strong> {{ $penjualan->customer->address }}</p>
            </div>
        </div>

        <h2>Invoice / Titipan Barang</h2>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Item</th>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Harga Satuan</th>
                    <th>Total</th>
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
                        <td>Rp. {{ number_format($rincian->price, 0, ',', '.') }}</td>
                        <td>Rp. {{ number_format($rincian->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="3" class="right">Total QTY</th>
                    <td>{{ $penjualan->rincianpenjualans->sum('quantity') }}</td>
                    <td>CTN</td>
                    <th>Total</th>
                    <td>Rp. {{ number_format($penjualan->total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="5" rowspan="4">
                        <strong>Tanggal Jatuh Tempo:</strong> {{ $penjualan->tenggat_waktu->format('d-m-Y') }}<br>
                        Keterangan: Pembayaran dapat ditransfer ke<br>
                        @if(($penjualan->tipe_ppn ?? 'Non PPN') === 'PPN')
                            1. BCA a/c 0107008881 Diatasnamakan CV Haathee<br>
                            2. Untuk Bilyet Giro atau cek ke BCA a/c 0107008881 Diatasnamakan CV Haathee<br>
                        @else
                            1. BCA a/c 0105999882 Diatasnamakan CV Haathee<br>
                            2. Untuk Bilyet Giro atau cek ke BCA a/c 0105999882 Diatasnamakan CV Haathee<br>
                        @endif
                        3. Maksimal Retur Barang dan Komplain Harga 7 Hari Kerja terhitung dari tanggal dikeluarkannya Invoice
                    </td>
                    <th>Diskon</th>
                    <td>Rp. {{ number_format(($penjualan->diskon / 100) * $penjualan->total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>DPP</th>
                    <td>Rp. {{ number_format($invoiceDpp, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>PPN</th>
                    <td>
                        @if($isPpnInvoice)
                            Rp. {{ number_format($invoicePpn, 0, ',', '.') }}
                        @else
                            Non PPN
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Total Netto</th>
                    <td>Rp. {{ number_format($penjualan->total_netto, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th colspan="2">Administrasi</th>
                    <th colspan="3">Accounting</th>
                    <th colspan="2">Finance</th>
                </tr>
                <tr class="signature-row">
                    <td colspan="2"></td>
                    <td colspan="3"></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
