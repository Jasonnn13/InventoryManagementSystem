<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function profit(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $selectedYear = $request->input('year', $currentYear); // Get year from request, default to current year

        // Data for the current month
        $penjualanTotalCurrentMonth = Penjualan::whereMonth('created_at', $currentMonth)
                                    ->whereYear('created_at', $currentYear)
                                    ->where('status', 'Lunas')
                                    ->sum('total_netto');

        $pembelianTotalCurrentMonth = Pembelian::whereMonth('created_at', $currentMonth)
                                    ->whereYear('created_at', $currentYear)
                                    ->sum('total');

        $profitCurrentMonth = $penjualanTotalCurrentMonth - $pembelianTotalCurrentMonth;

        // Check if a row for the current month exists in the 'laporan' table
        $laporan = Laporan::whereMonth('created_at', $currentMonth)
                          ->whereYear('created_at', $currentYear)
                          ->first();

        if ($laporan) {
            // Update the existing row
            $laporan->update([
                'pemasukan' => $penjualanTotalCurrentMonth,
                'pengeluaran' => $pembelianTotalCurrentMonth,
                'profit' => $profitCurrentMonth,
            ]);
        } else {
            // Create a new row
            Laporan::create([
                'pemasukan' => $penjualanTotalCurrentMonth,
                'pengeluaran' => $pembelianTotalCurrentMonth,
                'profit' => $profitCurrentMonth,
                'bulan' => $currentMonth,
                'tahun' => $currentYear,
            ]);
        }

        // Initialize arrays for data collection
        $months = [];   
        $penjualanData = [];
        $pembelianData = [];
        $profitData = [];

        // Loop through the past 12 months (including the current month)
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y'); // Add formatted month-year to the list for the chart labels

            // Retrieve existing Laporan for the specific month and year
            $laporan = Laporan::where('bulan', $date->month)
                              ->where('tahun', $date->year)
                              ->first();
            
            // Calculate totals for the specific month
            $penjualanTotal = Penjualan::whereMonth('created_at', $date->month)
                                        ->whereYear('created_at', $date->year)
                                        ->where('status', 'Lunas') // Ensure consistency by filtering 'Lunas' status
                                        ->sum('total_netto');

            $pembelianTotal = Pembelian::whereMonth('created_at', $date->month)
                                        ->whereYear('created_at', $date->year)
                                        ->sum('total');
                
            $profit = $penjualanTotal - $pembelianTotal;

            // Update or create Laporan record for the specific month
            if($laporan) {
                $laporan->update([
                    'pemasukan' => $penjualanTotal,
                    'pengeluaran' => $pembelianTotal,
                    'profit' => $profit,
                ]);
            } else {
                Laporan::create([
                    'pemasukan' => $penjualanTotal,
                    'pengeluaran' => $pembelianTotal,
                    'profit' => $profit,
                    'bulan' => $date->month,
                    'tahun' => $date->year,
                ]);
            }

            // Store the data for the charts
            $penjualanData[] = $penjualanTotal;
            $pembelianData[] = $pembelianTotal;
            $profitData[] = $profit;
        }

        // Fetch all laporan records filtered by selected year
        $laporans = Laporan::where('tahun', $selectedYear)->orderBy('bulan', 'desc')->get();

        // Get all available years for the dropdown
        $availableYears = Laporan::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return view('laporan.profit', compact('laporans', 'months', 'penjualanData', 'pembelianData', 'profitData', 'selectedYear', 'availableYears'));
    }
}
