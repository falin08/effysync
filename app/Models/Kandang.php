<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');  // Format sesuai kebutuhan Anda
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    public function getDeactivatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }
}
