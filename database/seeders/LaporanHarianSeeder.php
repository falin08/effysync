<?php

namespace Database\Seeders;

use App\Models\LaporanHarian;
use App\Models\Pakan;
use App\Models\Penyakit;
use App\Models\Kandang;
use App\Models\User;
use Illuminate\Database\Seeder;

class LaporanHarianSeeder extends Seeder
{
    public function run()
    {
        $user = User::first(); // Ganti dengan ID user yang sesuai
        $kandang = Kandang::first(); // Ganti dengan ID kandang yang sesuai
        $pakan = Pakan::first(); // Ganti dengan ID pakan yang sesuai
        $penyakit = Penyakit::first(); // Ganti dengan ID penyakit yang sesuai

        LaporanHarian::create([
            'id_kandang' => $kandang->id,
            'id_user' => $user->id,
            'sesi' => 'pagi',
            'id_pakan' => $pakan->id,
            'jumlah_pakan' => 50, // dalam kg
            'telur' => 10,
            'kematian' => 2,
            'jumlah_sakit' => 1,
            'id_penyakit' => $penyakit->id, // Penyakit pertama
        ]);

        LaporanHarian::create([
            'id_kandang' => $kandang->id,
            'id_user' => $user->id,
            'sesi' => 'sore',
            'id_pakan' => $pakan->id,
            'jumlah_pakan' => 30, // dalam kg
            'telur' => 8,
            'kematian' => 1,
            'jumlah_sakit' => 0,
            'id_penyakit' => null,
        ]);
    }
}