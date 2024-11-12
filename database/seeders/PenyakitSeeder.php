<?php

namespace Database\Seeders;

use App\Models\Penyakit;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PenyakitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create('id_ID');

        for ($i = 0; $i < 5; $i++) {
            Penyakit::create([
                'nama' => $faker->unique()->randomElement([
                    'Flu Burung', 
                    'Newcastle Disease', 
                    'Cacingan', 
                    'Kolibasilosis', 
                    'Snot'
                ]),
                'deskripsi' => $faker->sentence(10),
                'gejala' => $faker->sentence(8),
                'pengobatan' => $faker->sentence(8),
            ]);
        }
    }
}
