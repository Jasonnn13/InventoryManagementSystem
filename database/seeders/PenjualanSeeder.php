<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penjualan;
use App\Models\RincianPenjualan;
use App\Models\Customer;
use App\Models\Stock;
use App\Models\Gudang;
use App\Models\User;

class PenjualanSeeder extends Seeder
{
    public function run(): void
    {
        Penjualan::query()->delete();
        RincianPenjualan::query()->delete();

        $customer = Customer::first();
        $gudang = Gudang::first();
        $user = User::first();
        $stock = Stock::first();
        if (! $customer || ! $gudang || ! $user || ! $stock) return;

        $pen = Penjualan::create([
            'customers_id' => $customer->id,
            'total' => 70000,
            'tipe' => 'Cash',
            'tipe_ppn' => 'Non PPN',
            'total_netto' => 70000,
            'diskon' => 0,
            'ppn' => 0,
            'dpp' => 70000,
            'status' => 'Lunas',
            'sales' => $user->name,
            'tenggat_waktu' => now()->toDateString(),
            'users_id' => $user->id,
        ]);

        RincianPenjualan::create([
            'stocks_id' => $stock->id,
            'penjualan_id' => $pen->id,
            'quantity' => 10,
            'price' => $stock->jual,
            'total' => 10 * $stock->jual,
            'gudangs_id' => $gudang->id,
        ]);

        // Decrement stock
        $stock->decrement('stock', 2);
    }
}
