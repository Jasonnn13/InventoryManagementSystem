<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'stock', 'suppliers_id', 'jual', 'beli', 'kode'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'suppliers_id');
    }

    public function gudangs()
    {
        return $this->belongsToMany(Gudang::class, 'quantity', 'stocks_id', 'gudangs_id');
    }
    

    public function rincianPembelian()
    {
        return $this->hasMany(RincianPembelian::class, 'stocks_id');
    }
}
