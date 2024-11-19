<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LaporanHarian extends Model
{
    use HasFactory;

    protected $fillable =[
        'id_kandang',
        'id_user', 
        'id_pakan',
        'jumlah_pakan', 
        'telur', 
        'kematian', 
        'jumlah_sakit', 
        'id_penyakit',
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d F Y');  // Format sesuai kebutuhan Anda
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d F Y');
    }

    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'id_kandang');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function penyakit()
    {
        return $this->belongsTo(Penyakit::class, 'id_penyakit');
    }

    public function pakan()
    {
        return $this->belongsTo(Pakan::class, 'id_pakan');
    }
}
