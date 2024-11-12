<?php

namespace Database\Seeders;

use App\Models\Kandang;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KandangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create('id_ID');

        // Menambahkan 5 contoh data kandang
        for ($i = 0; $i < 5; $i++) {
            $status = $faker->randomElement(['aktif', 'tidak aktif']);
            Kandang::create([
                'kode' => $faker->unique()->word, // Nama kandang yang unik
                'jumlah_unggas' => $faker->numberBetween(1, 100), // Jumlah unggas antara 1 hingga 100
                'jenis_unggas' => $faker->randomElement(['Ayam', 'Bebek', 'Itik']), // Jenis unggas
                'status' => $status, // Status kandang
                'deactivated_at' => $status === 'tidak aktif' ? $faker->dateTimeThisYear() : null, // Tanggal deactive jika status tidak aktif
            ]);
        }
    }
}
