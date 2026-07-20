<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Monitoring BBM',
            'email' => 'admin@telkomakses.co.id',
            'password' => bcrypt('admin123'),
        ]);

        $this->call([
            PegawaiSeeder::class,
            KendaraanSeeder::class,
            PerjalananSeeder::class,
        ]);
    }
}
