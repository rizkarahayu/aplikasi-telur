<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $fillable = [
        'mode', 'jumlah', 'harga_satuan', 'total',  'produk_id'   // pastikan ini ada
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
