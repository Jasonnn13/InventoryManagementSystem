<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gudang;

class GudangsSeeder extends Seeder
{
    public function run(): void
    {
        Gudang::query()->delete();

        Gudang::create(['name' => 'Gudang Pusat']);
        Gudang::create(['name' => 'Gudang Cabang 1']);
    }
}
