<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerekModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'merek';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_merek',
        'kode_merek',
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

    // 🔗 Relasi: satu merek memiliki banyak kendaraan
    public function kendaraan()
    {
        return $this->hasMany(KendaraanModel::class, 'merek_id', 'id');
    }
}
