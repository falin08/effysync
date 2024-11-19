<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Penyakit extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'gejala',
        'pengobatan',
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');  // Format sesuai kebutuhan Anda
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    public function laporanHarians()
    {
        return $this->hasMany(LaporanHarian::class, 'id_penyakit');
    }
};
