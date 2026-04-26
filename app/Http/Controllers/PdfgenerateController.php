<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Penjualan;

class PdfgenerateController extends Controller
{
    public function generatePDF($penjualan_id)
    {
        // Retrieve only necessary data
        $penjualan = Penjualan::with('customer', 'rincianpenjualans.stock')->findOrFail($penjualan_id);

        // Share data to view
        $pdf = PDF::loadView('rincianpenjualan.invoice', ['penjualan' => $penjualan]);

        // // Download PDF file
        return $pdf->download('invoice_'.$penjualan_id.'.pdf');

        // return view('rincianpenjualan.invoice', compact('penjualan'));
    }

    public function generatePDF2($penjualan_id)
    {
        // Retrieve only necessary data
        $penjualan = Penjualan::with('customer', 'rincianpenjualans.stock')->findOrFail($penjualan_id);

        // Share data to view
        $pdf = PDF::loadView('rincianpenjualan.suratjalan', ['penjualan' => $penjualan]);

        return $pdf->download('surat_jalan_'.$penjualan_id.'.pdf');
    }


}
