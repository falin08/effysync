<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsTo(Penyakit::class, 'id_pakan');
    }
}
