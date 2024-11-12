<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
