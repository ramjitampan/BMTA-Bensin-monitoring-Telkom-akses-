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
        'status_efisiensi', 'fraud_flags', 'fraud_score',
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
        'fraud_flags'     => 'array', // JSON di DB
    ];

    public function pegawai()   { return $this->belongsTo(Pegawai::class); }
    public function kendaraan() { return $this->belongsTo(Kendaraan::class); }

    // =========================================================
    // LAYER 1 — Validasi Bon
    // =========================================================

    /**
     * Aturan bon lapangan: nominal harus kelipatan Rp1.000,
     * tetapi BUKAN kelipatan bulat Rp10.000.
     *
     * Contoh VALID   : 51.000, 52.000, 53.000, 127.000, 101.000
     * Contoh TIDAK VALID : 10.000, 20.000, 30.000, 50.000, 100.000
     *
     * Logika:
     *   1. Harus kelipatan 1.000
     *   2. BUKAN kelipatan 10.000
     */
    public static function isNominalGanjil(float $jumlah): bool
    {
        // Harus kelipatan 1.000
        if (fmod($jumlah, 1000) !== 0.0) {
            return false;
        }
        // Tolak jika kelipatan bulat 10.000
        return fmod($jumlah, 10000) !== 0.0;
    }

    /**
     * Cek duplikasi no_bon untuk kendaraan yang sama dalam periode tertentu.
     * Bon yang sama tidak mungkin dipakai dua kali.
     */
    public static function isDuplicateBon(string $noBon, int $kendaraanId, ?int $excludeId = null): bool
    {
        $query = static::where('no_bon', $noBon)
            ->where('kendaraan_id', $kendaraanId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    // =========================================================
    // LAYER 2 — Validasi Odometer
    // =========================================================

    /**
     * Ambil odometer terakhir tercatat untuk kendaraan ini.
     * Dipakai untuk validasi: km_lama baru harus >= km_baru record sebelumnya.
     */
    public static function getOdometerTerakhir(int $kendaraanId, ?int $excludeId = null): ?float
    {
        $query = static::where('kendaraan_id', $kendaraanId);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->orderByDesc('tanggal')->orderByDesc('id')->value('km_baru');
    }

    /**
     * Deteksi jarak tidak wajar berdasarkan rentang tanggal.
     * Default: max 600 km/hari untuk R4, 200 km/hari untuk R2.
     */
    public static function isJarakWajar(float $jarak, string $tipe, string $tanggal, int $kendaraanId, ?int $excludeId = null): bool
    {
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

        $avg = $data->avg();
        $variance = $data->map(fn($e) => pow($e - $avg, 2))->avg();
        $std = sqrt($variance);

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
     *   +30  Nominal bon kelipatan bulat 10.000
     *   +40  No bon duplikat
     *   +50  km_lama < odometer terakhir (odometer mundur = manipulasi)
     *   +25  Jarak hari ini melebihi batas wajar
     *   +35  Efisiensi outlier ke atas (z > +2, terlalu hemat → mencurigakan)
     *   +20  Efisiensi outlier ke bawah (z < -2, sangat boros)
     *   +15  Efisiensi di luar batas mutlak tipe kendaraan
     */
    public static function hitungFraudScore(array $data, ?int $excludeId = null): array
    {
        $flags = [];
        $score = 0;

        $kendaraan = Kendaraan::find($data['kendaraan_id']);
        $tipe = $kendaraan->tipe ?? 'R4';

        // --- Layer 1: Bon ---
        if (!static::isNominalGanjil($data['jumlah_biaya'])) {
            $flags[] = 'nominal_bon_tidak_ganjil';
            $score += 30;
        }

        if (!empty($data['no_bon']) && static::isDuplicateBon($data['no_bon'], $data['kendaraan_id'], $excludeId)) {
            $flags[] = 'no_bon_duplikat';
            $score += 40;
        }

        // --- Layer 2: Odometer ---
        $odometerTerakhir = static::getOdometerTerakhir($data['kendaraan_id'], $excludeId);
        if ($odometerTerakhir !== null && $data['km_lama'] < $odometerTerakhir) {
            $flags[] = 'odometer_mundur';
            $score += 50;
        }

        $jarak = $data['km_baru'] - $data['km_lama'];
        if (!static::isJarakWajar($jarak, $tipe, $data['tanggal'], $data['kendaraan_id'], $excludeId)) {
            $flags[] = 'jarak_melebihi_batas_harian';
            $score += 25;
        }

        // --- Layer 3: Efisiensi ---
        $efisiensi = $data['efisiensi'];
        $batas = static::getBatasEfisiensi($tipe);

        if ($efisiensi > $batas['anomali_atas'] || $efisiensi < $batas['anomali_bawah']) {
            $flags[] = 'efisiensi_di_luar_batas_mutlak';
            $score += 15;
        }

        $statistik = static::getStatistikEfisiensi($data['kendaraan_id'], $excludeId);
        $z = static::hitungZScore($efisiensi, $statistik);

        if ($z !== null) {
            if ($z > 2.0) {
                $flags[] = 'efisiensi_terlalu_tinggi_vs_historis';
                $score += 35;
            } elseif ($z < -2.0) {
                $flags[] = 'efisiensi_terlalu_rendah_vs_historis';
                $score += 20;
            }
        }

        return [
            'score' => $score,
            'flags' => $flags,
            'risk'  => static::interpretRisk($score),
        ];
    }

    public static function interpretRisk(int $score): string
    {
        if ($score === 0)    return 'aman';
        if ($score <= 20)    return 'perhatian';
        if ($score <= 50)    return 'mencurigakan';
        return 'tinggi';
    }

    public static function getBatasEfisiensi(string $tipe): array
    {
        return $tipe === 'R2'
            ? ['anomali_atas' => 60, 'balance' => 25, 'boros' => 10, 'anomali_bawah' => 3]
            : ['anomali_atas' => 20, 'balance' => 10, 'boros' => 5,  'anomali_bawah' => 2];
    }

    public static function tentukanStatus(float $efisiensi, string $tipe = 'R4'): string
    {
        $b = static::getBatasEfisiensi($tipe);
        if ($efisiensi > $b['anomali_atas'] || $efisiensi < $b['anomali_bawah']) return 'anomali';
        if ($efisiensi >= $b['balance'])  return 'balance';
        if ($efisiensi >= $b['boros'])    return 'boros';
        return 'anomali';
    }
}