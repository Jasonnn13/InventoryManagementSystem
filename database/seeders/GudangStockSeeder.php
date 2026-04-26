<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GudangStock;
use App\Models\Stock;
use App\Models\Gudang;

class GudangStockSeeder extends Seeder
{
    public function run(): void
    {
        GudangStock::query()->delete();

        $stocks = Stock::all();
        $gudangs = Gudang::all();
        foreach ($gudangs as $g) {
            foreach ($stocks as $s) {
                GudangStock::create([
                    'stocks_id' => $s->id,
                    'gudangs_id' => $g->id,
                    'quantity' => intval($s->stock / max(1, $gudangs->count())),
                ]);
            }
        }
    }
}
