<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KendaraanModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'kendaraan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_kendaraan',
        'harga',
        'stok',
        'deskripsi',
        'merek_id',
        'kategori_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected function serializeDate($date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    // 🔗 Relasi: kendaraan milik satu merek
    public function merek()
    {
        return $this->belongsTo(MerekModel::class, 'merek_id', 'id');
    }
}
