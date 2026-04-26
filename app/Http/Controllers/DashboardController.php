<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Penjualan;
use App\Models\User;
use App\Models\Pembelian;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $countReq = User::where('level', 0)->count();
        $countPiutang = Penjualan::where('status', 'Belum Lunas')->count();

        $penjualanCount = Penjualan::whereMonth('created_at', $currentMonth)
                                    ->whereYear('created_at', $currentYear)
                                    ->count();

        $pembelianCount = Pembelian::whereMonth('created_at', $currentMonth)
                                    ->whereYear('created_at', $currentYear)
                                    ->count();

        // Data for the last 12 months
        $months = [];
        $penjualanData = [];
        $pembelianData = [];
        $profitData = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::today()->startOfMonth()->subMonth($i);
            $year = $month->format('Y');
            $formattedMonth = $month->format('M');

            $penjualanTotal = Penjualan::whereMonth('created_at', $month->month)
                                        ->whereYear('created_at', $month->year)
                                        ->where('status', 'Lunas')
                                        ->sum(DB::raw('CASE WHEN total_netto IS NULL OR total_netto = 0 THEN total ELSE total_netto END'));

            $pembelianTotal = Pembelian::whereMonth('created_at', $month->month)
                                        ->whereYear('created_at', $month->year)
                                        ->sum('total');
                
            $profit = $penjualanTotal - $pembelianTotal;

            $months[] = $formattedMonth;
            $penjualanData[] = $penjualanTotal;
            $pembelianData[] = $pembelianTotal;
            $profitData[] = $profit;
        }

        return view('dashboard.index', compact('countPiutang', 'countReq', 'penjualanCount', 'pembelianCount', 'months', 'penjualanData', 'pembelianData', 'profitData'));
    }
}
