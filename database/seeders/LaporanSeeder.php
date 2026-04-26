<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Laporan;

class LaporanSeeder extends Seeder
{
    public function run(): void
    {
        Laporan::query()->delete();

        Laporan::create(['pemasukan' => 1000000, 'pengeluaran' => 400000, 'profit' => 600000, 'tahun' => now()->year, 'bulan' => now()->month]);
    }
}
