<?php
// app/Models/Perjalanan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perjalanan extends Model
{
    protected $fillable = [
        'pegawai_id', 'kendaraan_id', 'tanggal', 'tujuan', 'uraian',
        'km_lama', 'km_baru', 'jarak', 'vol_liter', 'harga_per_liter',
        'jumlah_biaya', 'no_bon', 'foto_bon', 'efisiensi',
        'status_efisiensi', 'status_reason', 'fraud_flags', 'fraud_score',
    ];

    protected $casts = [
        'tanggal'         => 'date',
        'km_lama'         => 'float',
        'km_baru'         => 'float',
        'jarak'           => 'float',
        'vol_liter'       => 'float',
        'harga_per_liter' => 'float',
        'jumlah_biaya'    => 'float',
        'efisiensi'       => 'float',
        'fraud_score'     => 'integer',
        'fraud_flags'     => 'array',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    // =========================================================
    // HELPER — Kalkulasi Turunan
    // Dipanggil di Controller agar store() dan update() identik.
    // =========================================================

    /**
     * Hitung jarak dari selisih odometer.
     */
    public static function hitungJarak(float $kmLama, float $kmBaru): float
    {
        return max(0.0, round($kmBaru - $kmLama, 2));
    }

    /**
     * Hitung volume BBM yang dibeli (liter) dari total biaya dan harga per liter.
     * Mengembalikan 0 jika harga_per_liter nol untuk menghindari division-by-zero.
     */
    public static function hitungVolumeLiter(float $jumlahBiaya, float $hargaPerLiter): float
    {
        if ($hargaPerLiter <= 0) {
            return 0.0;
        }

        return round($jumlahBiaya / $hargaPerLiter, 2);
    }

    /**
     * Hitung efisiensi BBM (km/liter).
     * Mengembalikan 0 jika volume nol.
     */
    public static function hitungEfisiensi(float $jarak, float $volLiter): float
    {
        if ($volLiter <= 0) {
            return 0.0;
        }

        return round($jarak / $volLiter, 2);
    }

    /**
     * Buat penjelasan naratif untuk status efisiensi kendaraan.
     * Disimpan di kolom status_reason agar audit trail lebih informatif.
     *
     * @param  float  $efisiensi  Nilai efisiensi km/liter
     * @param  string $tipe       Tipe kendaraan: 'R2' | 'R4'
     * @param  string $status     Hasil tentukanStatus()
     * @param  float|null $avgHistoris Rata-rata historis kendaraan (opsional)
     */
    public static function generateStatusReason(
        float   $efisiensi,
        string  $tipe,
        string  $status,
        ?float  $avgHistoris = null
    ): string {
        $b    = static::getBatasEfisiensi($tipe);
        $unit = 'km/liter';

        $historisInfo = $avgHistoris !== null
            ? sprintf(' (rata-rata historis kendaraan: %.1f %s)', $avgHistoris, $unit)
            : '';

        return match ($status) {
            'balance' => sprintf(
                'Efisiensi %.2f %s tergolong normal (batas normal ≥ %.0f %s untuk %s)%s.',
                $efisiensi, $unit, $b['balance'], $unit, $tipe, $historisInfo
            ),
            'boros' => sprintf(
                'Efisiensi %.2f %s di bawah normal (batas normal %.0f %s, batas boros %.0f %s untuk %s)%s. Konsumsi BBM lebih tinggi dari standar.',
                $efisiensi, $unit, $b['balance'], $unit, $b['boros'], $unit, $tipe, $historisInfo
            ),
            'anomali' => $efisiensi > $b['anomali_atas']
                ? sprintf(
                    'Efisiensi %.2f %s melebihi batas atas anomali (%.0f %s untuk %s)%s. Diduga manipulasi data atau kesalahan input.',
                    $efisiensi, $unit, $b['anomali_atas'], $unit, $tipe, $historisInfo
                )
                : sprintf(
                    'Efisiensi %.2f %s di bawah batas minimum (%.0f %s untuk %s)%s. Konsumsi BBM sangat tidak wajar.',
                    $efisiensi, $unit, $b['anomali_bawah'], $unit, $tipe, $historisInfo
                ),
            default => sprintf('Efisiensi %.2f %s tidak dapat dikategorikan.', $efisiensi, $unit),
        };
    }

    // =========================================================
    // LAYER 1 — Validasi Bon
    // =========================================================

    /**
     * Aturan bon lapangan: nominal harus kelipatan Rp1.000,
     * tetapi BUKAN kelipatan bulat Rp10.000.
     *
     * Contoh VALID    : 51.000, 52.000, 53.000, 127.000, 101.000
     * Contoh TIDAK VALID: 10.000, 20.000, 30.000, 50.000, 100.000
     *
     * Catatan implementasi: konversi ke integer sebelum fmod()
     * untuk menghindari floating-point precision error.
     */
    public static function isNominalGanjil(float $jumlah): bool
    {
        $jumlahInt = (int) round($jumlah);

        if ($jumlahInt % 1000 !== 0) {
            return false;
        }

        return $jumlahInt % 10000 !== 0;
    }

    /**
     * Cek duplikasi no_bon untuk kendaraan yang sama.
     * Bon yang sama tidak mungkin dipakai dua kali pada kendaraan yang sama.
     */
    public static function isDuplicateBon(string $noBon, int $kendaraanId, ?int $excludeId = null): bool
    {
        return static::where('no_bon', $noBon)
            ->where('kendaraan_id', $kendaraanId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }

    // =========================================================
    // LAYER 2 — Validasi Odometer
    // =========================================================

    /**
     * Ambil odometer terakhir tercatat untuk kendaraan ini.
     *
     * Catatan: fungsi ini TIDAK lagi dipakai sebagai syarat validasi penyimpanan.
     * Admin menginput data berdasarkan bon BBM yang diterima tanpa mengetahui
     * urutan perjalanan dalam satu hari, sehingga urutan input tidak mencerminkan
     * urutan kronologis perjalanan kendaraan.
     *
     * Fungsi ini dipertahankan untuk keperluan informatif di halaman create
     * (menampilkan referensi KM terakhir tercatat kepada admin).
     */
    public static function getOdometerTerakhir(int $kendaraanId, ?int $excludeId = null): ?float
    {
        return static::where('kendaraan_id', $kendaraanId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->value('km_baru');
    }

    /**
     * Deteksi jarak tidak wajar berdasarkan rentang tanggal.
     * Default: max 600 km/hari untuk R4, 200 km/hari untuk R2.
     */
    public static function isJarakWajar(
        float   $jarak,
        string  $tipe,
        string  $tanggal,
        int     $kendaraanId,
        ?int    $excludeId = null
    ): bool {
        $maxPerHari = $tipe === 'R2' ? 200 : 600;

        $jarakHariIni = static::where('kendaraan_id', $kendaraanId)
            ->whereDate('tanggal', $tanggal)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->sum('jarak');

        return ($jarakHariIni + $jarak) <= $maxPerHari;
    }

    // =========================================================
    // LAYER 3 — Analisis Efisiensi Statistik
    // =========================================================

    /**
     * Ambil rata-rata dan standar deviasi efisiensi historis kendaraan ini.
     * Hanya dari data yang statusnya bukan anomali (data bersih).
     */
    public static function getStatistikEfisiensi(int $kendaraanId, ?int $excludeId = null): array
    {
        $data = static::where('kendaraan_id', $kendaraanId)
            ->where('status_efisiensi', '!=', 'anomali')
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->pluck('efisiensi');

        if ($data->count() < 3) {
            return ['avg' => null, 'std' => null, 'count' => $data->count()];
        }

        $avg      = $data->avg();
        $variance = $data->map(fn($e) => pow($e - $avg, 2))->avg();
        $std      = sqrt($variance);

        return ['avg' => $avg, 'std' => $std, 'count' => $data->count()];
    }

    /**
     * Deteksi outlier efisiensi menggunakan Z-score.
     * Jika efisiensi menyimpang lebih dari 2 standar deviasi dari rata-rata
     * historis kendaraan, ini mencurigakan.
     */
    public static function hitungZScore(float $efisiensi, array $statistik): ?float
    {
        if ($statistik['avg'] === null || $statistik['std'] == 0) {
            return null;
        }

        return ($efisiensi - $statistik['avg']) / $statistik['std'];
    }

    // =========================================================
    // FRAUD SCORE — Akumulasi semua flag
    // =========================================================

    /**
     * Jalankan semua pemeriksaan, kumpulkan flag, hitung skor.
     * Skor 0 = bersih, semakin tinggi semakin mencurigakan.
     *
     * Skor per flag:
     *   +30  Nominal bon kelipatan bulat Rp10.000
     *   +40  No bon duplikat
     *   +25  Jarak hari ini melebihi batas wajar
     *   +35  Efisiensi outlier ke atas (z > +2, terlalu hemat → mencurigakan)
     *   +20  Efisiensi outlier ke bawah (z < -2, sangat boros)
     *   +15  Efisiensi di luar batas mutlak tipe kendaraan
     *
     * Catatan: flag 'odometer_mundur' (+50) telah dihapus karena tidak sesuai
     * proses bisnis. Admin menginput bon tanpa mengetahui urutan perjalanan
     * dalam satu hari, sehingga perbandingan KM Awal terhadap KM terakhir
     * di database menghasilkan false positive yang tinggi.
     *
     * @param  array    $data       Wajib: kendaraan_id, jumlah_biaya, no_bon,
     *                              km_lama, km_baru, tanggal, jarak, efisiensi
     * @param  ?int     $excludeId  ID record yang sedang di-update (hindari self-compare)
     */
    public static function hitungFraudScore(array $data, ?int $excludeId = null): array
    {
        $flags = [];
        $score = 0;

        $kendaraan = Kendaraan::find($data['kendaraan_id']);
        $tipe      = $kendaraan->jenis ?? 'R4';

        // --- Layer 1: Bon ---
        if (!static::isNominalGanjil((float) $data['jumlah_biaya'])) {
            $flags[] = 'nominal_bon_tidak_ganjil';
            $score  += 30;
        }

        if (!empty($data['no_bon']) && static::isDuplicateBon($data['no_bon'], $data['kendaraan_id'], $excludeId)) {
            $flags[] = 'no_bon_duplikat';
            $score  += 40;
        }

        // --- Layer 2: Jarak harian ---
        // Catatan: validasi odometer_mundur dihapus — lihat docblock hitungFraudScore().
        if (!static::isJarakWajar((float) $data['jarak'], $tipe, $data['tanggal'], $data['kendaraan_id'], $excludeId)) {
            $flags[] = 'jarak_melebihi_batas_harian';
            $score  += 25;
        }

        // --- Layer 3: Efisiensi ---
        $efisiensi = (float) $data['efisiensi'];
        $batas     = static::getBatasEfisiensi($tipe);

        if ($efisiensi > $batas['anomali_atas'] || $efisiensi < $batas['anomali_bawah']) {
            $flags[] = 'efisiensi_di_luar_batas_mutlak';
            $score  += 15;
        }

        $statistik = static::getStatistikEfisiensi($data['kendaraan_id'], $excludeId);
        $z         = static::hitungZScore($efisiensi, $statistik);

        if ($z !== null) {
            if ($z > 2.0) {
                $flags[] = 'efisiensi_terlalu_tinggi_vs_historis';
                $score  += 35;
            } elseif ($z < -2.0) {
                $flags[] = 'efisiensi_terlalu_rendah_vs_historis';
                $score  += 20;
            }
        }

        return [
            'score' => $score,
            'flags' => $flags,
            'risk'  => static::interpretRisk($score),
        ];
    }

    // =========================================================
    // HELPER — Klasifikasi & Batas
    // =========================================================

    public static function interpretRisk(int $score): string
    {
        return match (true) {
            $score === 0  => 'aman',
            $score <= 20  => 'perhatian',
            $score <= 50  => 'mencurigakan',
            default       => 'tinggi',
        };
    }

    /**
     * Batas efisiensi per tipe kendaraan (km/liter).
     *
     * R2 (motor): anomali_atas=60, balance=25, boros=10, anomali_bawah=3
     * R4 (mobil): anomali_atas=20, balance=10, boros=5,  anomali_bawah=2
     */
    public static function getBatasEfisiensi(string $tipe): array
    {
        return $tipe === 'R2'
            ? ['anomali_atas' => 60, 'balance' => 25, 'boros' => 10, 'anomali_bawah' => 3]
            : ['anomali_atas' => 20, 'balance' => 10, 'boros' => 5,  'anomali_bawah' => 2];
    }

    /**
     * Tentukan status efisiensi berdasarkan nilai dan tipe kendaraan.
     *
     * Urutan pengecekan penting: anomali diperiksa dulu (batas absolut),
     * baru balance dan boros di range normal.
     */
    public static function tentukanStatus(float $efisiensi, string $tipe = 'R4'): string
    {
        $b = static::getBatasEfisiensi($tipe);

        if ($efisiensi > $b['anomali_atas'] || $efisiensi < $b['anomali_bawah']) {
            return 'anomali';
        }

        if ($efisiensi >= $b['balance']) {
            return 'balance';
        }

        if ($efisiensi >= $b['boros']) {
            return 'boros';
        }

        // Nilai antara anomali_bawah dan boros — masuk kategori anomali bawah
        return 'anomali';
    }
}