<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'kategori';
    protected $primaryKey = 'id';

    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
        'deskripsi',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate($date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    // 🔗 Relasi: satu kategori memiliki banyak kendaraan
    public function kendaraan()
    {
        return $this->hasMany(KendaraanModel::class, 'kategori_id', 'id');
    }
}
