<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SuppliersSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::query()->delete();

        Supplier::create(['name' => 'PT. Sumber Makmur', 'address' => 'Jl. Industri 12, Jakarta', 'contact_information' => '021-555-0101']);
        Supplier::create(['name' => 'CV. Bahan Jaya', 'address' => 'Komplek Niaga 4, Bandung', 'contact_information' => '022-666-0202']);
        Supplier::create(['name' => 'UD. Sentosa', 'address' => 'Jl. Raya No.5, Surabaya', 'contact_information' => '031-777-0303']);
    }
}
