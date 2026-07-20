<?php

namespace Database\Seeders;

use App\Models\Kendaraan;
use Illuminate\Database\Seeder;

class KendaraanSeeder extends Seeder
{
    public function run(): void
    {
        $kendaraans = [
            ['plat_nomor' => 'BK 1234 AB', 'merk' => 'Toyota Avanza',   'jenis' => 'R4', 'tahun' => 2020],
            ['plat_nomor' => 'BK 5678 CD', 'merk' => 'Mitsubishi L300', 'jenis' => 'R4', 'tahun' => 2019],
            ['plat_nomor' => 'BK 9101 EF', 'merk' => 'Isuzu Elf',       'jenis' => 'R6', 'tahun' => 2021],
            ['plat_nomor' => 'BK 1122 GH', 'merk' => 'Suzuki Carry',    'jenis' => 'R4', 'tahun' => 2022],
            ['plat_nomor' => 'BK 3344 IJ', 'merk' => 'Mitsubishi Colt Diesel', 'jenis' => 'R6', 'tahun' => 2020],
            ['plat_nomor' => 'BK 5566 KL', 'merk' => 'Honda CR-V',      'jenis' => 'R4', 'tahun' => 2023],
        ];

        foreach ($kendaraans as $kendaraan) {
            Kendaraan::create($kendaraan);
        }
    }
}
