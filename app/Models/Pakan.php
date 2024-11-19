<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pakan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jenis',
        'stok',
    ];

    public function laporanHarians()
    {
        return $this->hasMany(LaporanHarian::class, 'id_pakan');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');  // Format sesuai kebutuhan Anda
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

}
