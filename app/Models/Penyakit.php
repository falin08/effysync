<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penyakit extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'gejala',
        'pengobatan',
    ];

    public function laporanHarians()
    {
        return $this->hasMany(LaporanHarian::class, 'id_penyakit');
    }
};
