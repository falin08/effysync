<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    protected $casts = [
        'deactivated_at' => 'datetime',
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
