<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
            SuppliersSeeder::class,
            GudangsSeeder::class,
            CustomersSeeder::class,
            StocksSeeder::class,
            GudangStockSeeder::class,
            PembelianSeeder::class,
            PenjualanSeeder::class,
            LaporanSeeder::class,
        ]);
    }
}
