<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pembelian;
use App\Models\RincianPembelian;
use App\Models\Supplier;
use App\Models\Stock;
use App\Models\Gudang;
use App\Models\User;

class PembelianSeeder extends Seeder
{
    public function run(): void
    {
        Pembelian::query()->delete();
        RincianPembelian::query()->delete();

        $supplier = Supplier::first();
        $gudang = Gudang::first();
        $user = User::first();
        $stock = Stock::first();
        if (! $supplier || ! $gudang || ! $user || ! $stock) return;

        $p = Pembelian::create([
            'suppliers_id' => $supplier->id,
            'total' => 500000,
            'users_id' => $user->id,
            'gudangs_id' => $gudang->id,
        ]);

        RincianPembelian::create([
            'stocks_id' => $stock->id,
            'pembelian_id' => $p->id,
            'quantity' => 50,
            'price' => $stock->beli,
            'total' => 50 * $stock->beli,
        ]);

        // Update stock count
        $stock->increment('stock', 50);
    }
}
