<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RincianPembelian extends Model
{
    use HasFactory;

    protected $table = 'rincianpembelian';

    protected $fillable = ['stocks_id', 'pembelian_id', 'quantity', 'price', 'total'];

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stocks_id');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudangs_id');
    }

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }
}
