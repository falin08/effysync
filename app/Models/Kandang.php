<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kandang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'jumlah_unggas',
        'jenis_unggas',
        'status',
        'deactivated_at',
    ];

    public function isActive()
    {
        return $this->deactivated_at === null;
    }

    public function laporanHarians()
    {
        return $this->hasMany(LaporanHarian::class, 'id_kandang');
    }
}
