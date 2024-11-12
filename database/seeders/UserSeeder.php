<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $faker = Factory::create('id_ID');

        User::factory()->create([
            'name' => $faker->name(),
            'email' => 'admin@poultrease.com',
            'username' => 'admin',
            'role' => 'admin',
            'password' => bcrypt('123'),
        ]);

        User::factory()->create([
            'name' => $faker->name(),
            'email' => 'customer@gmail.com',
            'username' => 'user',
            'role' => 'user',
            'password' => bcrypt('123'),
        ]);

        User::factory()->create([
            'name' => $faker->name(),
            'email' => 'user1@gmail.com',
            'username' => 'user1',
            'role' => 'user',
            'password' => bcrypt('123'),
        ]);
    }
}
