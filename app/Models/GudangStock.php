<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GudangStock extends Model
{
    protected $table = 'gudang_stock';

    protected $fillable = [
        'stocks_id',
        'gudangs_id',
        'quantity',
    ];


    public function stock() {
        return $this->belongsTo(Stock::class, 'stocks_id');
    }

    public function gudang() {
        return $this->belongsTo(Gudang::class, 'gudangs_id');
    }
}
