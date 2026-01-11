<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiModel extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'kendaraan_id',
        'tanggal_transaksi',
        'total_harga',
        'status',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(KendaraanModel::class, 'kendaraan_id');
    }
}
