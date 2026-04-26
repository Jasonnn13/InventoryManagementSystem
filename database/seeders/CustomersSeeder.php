<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomersSeeder extends Seeder
{
    public function run(): void
    {
        Customer::query()->delete();

        Customer::create(['name' => 'PT. Pelanggan Satu', 'address' => 'Jl. Pelanggan 1', 'contact_information' => '0812-1111-2222']);
        Customer::create(['name' => 'CV. Toko Dua', 'address' => 'Jl. Pelanggan 2', 'contact_information' => '0812-3333-4444']);
        Customer::create(['name' => 'Toko Kecil', 'address' => 'Jl. Kecil 3', 'contact_information' => '0812-5555-6666']);
    }
}
