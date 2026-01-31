<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $fillable = ['gudangs_id', 'customers_id', 'total', 'users_id', 'status', 'tenggat_waktu', 'sales', 'diskon', 'ppn', 'total_netto', 'dpp', 'tipe'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customers_id');
    }

    protected $casts = [
        'tenggat_waktu' => 'date',
    ];

    // In penjualan.php
    public function rincianPenjualans()
    {
        return $this->hasMany(RincianPenjualan::class, 'penjualan_id');
    }

    public function gudang()
    {
        return $this->hasMany(Gudang::class, 'gudangs_id');
    }


    public function user()
{
    return $this->belongsTo(User::class, 'users_id');
}

}
