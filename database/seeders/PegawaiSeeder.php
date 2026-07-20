<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $pegawais = [
            ['nama' => 'Ahmad Fauzi', 'jabatan' => 'Manager Operasional', 'divisi' => 'Operasional', 'no_hp' => '081234567890'],
            ['nama' => 'Rina Marlina', 'jabatan' => 'Supervisor Logistik', 'divisi' => 'Logistik', 'no_hp' => '081298765432'],
            ['nama' => 'Budi Santoso', 'jabatan' => 'Staff Operasional', 'divisi' => 'Operasional', 'no_hp' => '082134567891'],
            ['nama' => 'Dewi Sartika', 'jabatan' => 'Staff Administrasi', 'divisi' => 'Administrasi', 'no_hp' => '082145678912'],
            ['nama' => 'Eko Prasetyo', 'jabatan' => 'Driver Operasional', 'divisi' => 'Operasional', 'no_hp' => '083156789123'],
            ['nama' => 'Fitri Handayani', 'jabatan' => 'Staff Logistik', 'divisi' => 'Logistik', 'no_hp' => '083167891234'],
        ];

        foreach ($pegawais as $pegawai) {
            Pegawai::create($pegawai);
        }
    }
}
