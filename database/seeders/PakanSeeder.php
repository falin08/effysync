<?php

namespace Database\Seeders;

use App\Models\Pakan;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PakanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create('id_ID');

        for ($i = 0; $i < 5; $i++) { // Menghasilkan 5 entri pakan
            Pakan::create([
                'nama' => $faker->unique()->word, // Nama pakan
                'jenis' => $faker->randomElement(['Grower', 'Starter', 'Finisher']), // Jenis pakan
                'stok' => $faker->numberBetween(10, 100), // Stok pakan (antara 10 hingga 100)
            ]);
        }
    }
}
