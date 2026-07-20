<?php

namespace Database\Seeders;

use App\Models\Kendaraan;
use App\Models\Pegawai;
use App\Models\Perjalanan;
use Illuminate\Database\Seeder;

class PerjalananSeeder extends Seeder
{
    private array $kendaraanOdometer = [];

    public function run(): void
    {
        $pegawais = Pegawai::pluck('id')->toArray();
        $kendaraans = Kendaraan::all();

        $trips = [];
        $date = now()->subMonths(3)->startOfDay();

        foreach ($kendaraans as $k) {
            $km = [2000, 5000, 10000, 8000, 15000, 3000][array_search($k->id, Kendaraan::pluck('id')->toArray())];
            $this->kendaraanOdometer[$k->id] = $km;
        }

        // Trip 1-3: Kendaraan 1 (Avanza) — normal
        $pegawai1 = $pegawais[array_rand($pegawais)];
        $trips[] = $this->makeTrip(1, $pegawai1, $date->copy()->addDays(2), 120, 90, 'Pertalite', 10000, 'Dinas ke Kantor Cabang');
        $trips[] = $this->makeTrip(1, $pegawai1, $date->copy()->addDays(5), 85, 80, 'Pertalite', 10000, 'Sosialisasi Lapangan');
        $trips[] = $this->makeTrip(1, $pegawai1, $date->copy()->addDays(9), 200, 150, 'Pertalite', 10000, 'Monitoring Proyek');

        // Trip 4-6: Kendaraan 2 (L300) — normal
        $pegawai2 = $pegawais[array_rand($pegawais)];
        $trips[] = $this->makeTrip(2, $pegawai2, $date->copy()->addDays(3), 60, 45, 'Solar', 6800, 'Pengiriman Logistik');
        $trips[] = $this->makeTrip(2, $pegawai2, $date->copy()->addDays(7), 95, 50, 'Solar', 6800, 'Ambil Material');
        $trips[] = $this->makeTrip(2, $pegawai2, $date->copy()->addDays(12), 150, 130, 'Solar', 6800, 'Distribusi ke UPT');

        // Trip 7-9: Kendaraan 3 (Elf) — normal
        $pegawai3 = $pegawais[array_rand($pegawais)];
        $trips[] = $this->makeTrip(3, $pegawai3, $date->copy()->addDays(4), 180, 140, 'Solar', 6800, 'Angkut Peralatan');
        $trips[] = $this->makeTrip(3, $pegawai3, $date->copy()->addDays(8), 220, 160, 'Solar', 6800, 'Pengiriman ke Gudang');
        $trips[] = $this->makeTrip(3, $pegawai3, $date->copy()->addDays(14), 90, 70, 'Solar', 6800, 'Servis Kendaraan');

        // Trip 10-12: Kendaraan 4 (Carry) — normal
        $pegawai4 = $pegawais[array_rand($pegawais)];
        $trips[] = $this->makeTrip(4, $pegawai4, $date->copy()->addDays(6), 75, 55, 'Pertalite', 10000, 'Survey Lokasi');
        $trips[] = $this->makeTrip(4, $pegawai4, $date->copy()->addDays(11), 130, 100, 'Pertalite', 10000, 'Pengiriman Dokumen');
        $trips[] = $this->makeTrip(4, $pegawai4, $date->copy()->addDays(16), 50, 40, 'Pertalite', 10000, 'Koordinasi Lapangan');

        // Trip 13-15: Kendaraan 5 (Colt Diesel) — normal
        $pegawai5 = $pegawais[array_rand($pegawais)];
        $trips[] = $this->makeTrip(5, $pegawai5, $date->copy()->addDays(10), 300, 250, 'Solar', 6800, 'Angkut Beban Berat');
        $trips[] = $this->makeTrip(5, $pegawai5, $date->copy()->addDays(15), 250, 140, 'Solar', 6800, 'Distribusi Material');
        $trips[] = $this->makeTrip(5, $pegawai5, $date->copy()->addDays(20), 180, 120, 'Solar', 6800, 'Pengiriman Alat Berat');

        // Trip 16-18: Kendaraan 6 (CR-V) — normal
        $pegawai6 = $pegawais[array_rand($pegawais)];
        $trips[] = $this->makeTrip(6, $pegawai6, $date->copy()->addDays(13), 100, 80, 'Pertalite', 10000, 'Rapat Koordinasi');
        $trips[] = $this->makeTrip(6, $pegawai6, $date->copy()->addDays(18), 160, 130, 'Pertalite', 10000, 'Inspeksi Lapangan');
        $trips[] = $this->makeTrip(6, $pegawai6, $date->copy()->addDays(22), 90, 70, 'Pertalite', 10000, 'Kunjungan Mitra');

        // Trip 19-24: Data dengan ANOMALI untuk demo fraud detection
        // Anomali 1: Jarak tidak wajar — Avanza (pertalite) tiba-tiba "jalan" 500km
        $trips[] = $this->makeTrip(1, $pegawai1, $date->copy()->addDays(25), 500, 85, 'Pertalite', 10000, 'Perjalanan Luar Kota', 'anomali');

        // Anomali 2: Efisiensi sangat rendah — L300 tiba-tiba boros
        $trips[] = $this->makeTrip(2, $pegawai2, $date->copy()->addDays(28), 40, 50, 'Solar', 6800, 'Ambil Dokumen', 'anomali');

        // Anomali 3: Nominal bon bulat (indikasi fraud) — Elf
        $trips[] = $this->makeTrip(3, $pegawai3, $date->copy()->addDays(30), 160, 80, 'Solar', 6800, 'Pengirinan Rutin', 'anomali');

        // Anomali 4: Harga BBM tidak wajar — Carry
        $trips[] = $this->makeTrip(4, $pegawai4, $date->copy()->addDays(32), 90, 55, 'Pertalite', 25000, 'Perjalanan Dinas', 'anomali');

        // Perlu Verifikasi: Deviasi sedang — CR-V
        $trips[] = $this->makeTrip(6, $pegawai6, $date->copy()->addDays(35), 250, 50, 'Pertalite', 10000, 'Perjalanan Verifikasi', 'boros');

        // Boros normal — Colt Diesel
        $trips[] = $this->makeTrip(5, $pegawai5, $date->copy()->addDays(38), 200, 95, 'Solar', 6800, 'Angkut Tambahan', 'balance');

        foreach ($trips as $trip) {
            Perjalanan::create($trip);
        }
    }

    private function makeTrip(
        int    $kendaraanId,
        int    $pegawaiId,
        \Carbon\Carbon $tanggal,
        float  $jarak,
        float  $volLiter,
        string $bbm,
        float  $hargaPerLiter,
        string $tujuan,
        string $statusEfisiensi = 'balance'
    ): array {
        $kmLama = $this->kendaraanOdometer[$kendaraanId];
        $kmBaru = $kmLama + $jarak;
        $this->kendaraanOdometer[$kendaraanId] = $kmBaru;

        $efisiensi = $volLiter > 0 ? round($jarak / $volLiter, 2) : 0;
        $jumlahBiaya = round($volLiter * $hargaPerLiter);

        // Harga BBM menyesuaikan
        if ($bbm === 'Pertalite') {
            $hargaPerLiter = 10000;
        } else {
            $hargaPerLiter = 6800;
        }
        $jumlahBiaya = round($volLiter * $hargaPerLiter);

        return [
            'pegawai_id'       => $pegawaiId,
            'kendaraan_id'     => $kendaraanId,
            'tanggal'          => $tanggal->format('Y-m-d'),
            'tujuan'           => $tujuan,
            'uraian'           => null,
            'km_lama'          => $kmLama,
            'km_baru'          => $kmBaru,
            'jarak'            => $jarak,
            'vol_liter'        => $volLiter,
            'harga_per_liter'  => $hargaPerLiter,
            'jumlah_biaya'     => $jumlahBiaya,
            'no_bon'           => 'BN/' . $tanggal->format('ymd') . '/' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'foto_bon'         => null,
            'efisiensi'        => $efisiensi,
            'status_efisiensi' => $statusEfisiensi,
            'fraud_score'      => 0,
            'fraud_flags'      => null,
        ];
    }
}
