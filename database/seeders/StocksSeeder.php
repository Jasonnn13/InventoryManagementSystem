<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\Supplier;

class StocksSeeder extends Seeder
{
    public function run(): void
    {
        Stock::query()->delete();

        $suppliers = Supplier::all();
        if ($suppliers->isEmpty()) return;

        $items = [
            ['name' => 'Pipa PVC 1"', 'kode' => 'PVC1001', 'stock' => 100, 'beli' => 5000, 'jual' => 7000],
            ['name' => 'Paku 2 inch', 'kode' => 'PAKU2002', 'stock' => 500, 'beli' => 100, 'jual' => 150],
            ['name' => 'Cat Tembok 5kg', 'kode' => 'CAT5005', 'stock' => 50, 'beli' => 80000, 'jual' => 100000],
            ['name' => 'Kabel 2m', 'kode' => 'KBL2002', 'stock' => 200, 'beli' => 15000, 'jual' => 20000],
            ['name' => 'Sekrup 10mm', 'kode' => 'SKP1010', 'stock' => 400, 'beli' => 200, 'jual' => 300],
            ['name' => 'Meteran 5m', 'kode' => 'MTR5005', 'stock' => 75, 'beli' => 25000, 'jual' => 35000],
        ];

        foreach ($items as $i => $it) {
            $supplier = $suppliers[$i % $suppliers->count()];
            Stock::create(array_merge($it, ['suppliers_id' => $supplier->id]));
        }
    }
}
