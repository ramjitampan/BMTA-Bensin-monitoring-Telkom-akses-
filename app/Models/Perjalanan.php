<?php

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
    // SCOPES — Urutan berdasarkan timeline kendaraan
    // =========================================================

    public function scopeOrderByVehicleTimeline($query)
    {
        return $query->orderBy('kendaraan_id')->orderBy('km_baru');
    }

    // =========================================================
    // HELPERS — Kalkulasi Turunan
    // =========================================================

    public static function hitungJarak(float $kmLama, float $kmBaru): float
    {
        return max(0.0, round($kmBaru - $kmLama, 2));
    }

    public static function hitungVolumeLiter(float $jumlahBiaya, float $hargaPerLiter): float
    {
        if ($hargaPerLiter <= 0) {
            return 0.0;
        }

        return round($jumlahBiaya / $hargaPerLiter, 2);
    }

    public static function hitungEfisiensi(float $jarak, float $volLiter): float
    {
        if ($volLiter <= 0) {
            return 0.0;
        }

        return round($jarak / $volLiter, 2);
    }

    public static function generateStatusReason(
        float  $efisiensi,
        string $tipe,
        string $status,
        ?string $bbm = null
    ): string {
        $b    = static::getBatasEfisiensi($tipe, $bbm);
        $unit = 'km/liter';
        $bbmLabel = $bbm ? strtoupper($bbm) : ($tipe === 'R2' ? 'BENSIN' : 'BENSIN');

        return match ($status) {
            'balance' => sprintf(
                'Efisiensi %.2f %s tergolong normal untuk %s %s (batas ≥ %.0f %s).',
                $efisiensi, $unit, $tipe, $bbmLabel, $b['balance'], $unit
            ),
            'boros' => sprintf(
                'Efisiensi %.2f %s di bawah normal untuk %s %s (batas normal %.0f %s). Konsumsi BBM lebih tinggi dari standar.',
                $efisiensi, $unit, $tipe, $bbmLabel, $b['balance'], $unit
            ),
            'anomali' => $efisiensi > $b['anomali_atas']
                ? sprintf(
                    'Efisiensi %.2f %s melebihi batas atas anomali (%.0f %s untuk %s %s). Perlu verifikasi data.',
                    $efisiensi, $unit, $b['anomali_atas'], $unit, $tipe, $bbmLabel
                )
                : sprintf(
                    'Efisiensi %.2f %s di bawah batas minimum untuk %s %s (%.0f %s). Konsumsi BBM tidak wajar.',
                    $efisiensi, $unit, $tipe, $bbmLabel, $b['anomali_bawah'], $unit
                ),
            default => sprintf('Efisiensi %.2f %s tidak dapat dikategorikan.', $efisiensi, $unit),
        };
    }

    // =========================================================
    // VALIDASI BON
    // =========================================================

    public static function isNominalGanjil(float $jumlah): bool
    {
        $jumlahInt = (int) round($jumlah);

        if ($jumlahInt % 1000 !== 0) {
            return false;
        }

        return $jumlahInt % 10000 !== 0;
    }

    public static function isDuplicateBon(string $noBon, int $kendaraanId, ?int $excludeId = null): bool
    {
        return static::where('no_bon', $noBon)
            ->where('kendaraan_id', $kendaraanId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }

    public static function isDuplicateRecord(
        string $tanggal,
        int $kendaraanId,
        float $kmLama,
        float $kmBaru,
        float $volLiter,
        ?int $excludeId = null
    ): bool {
        return static::whereDate('tanggal', $tanggal)
            ->where('kendaraan_id', $kendaraanId)
            ->where('km_lama', $kmLama)
            ->where('km_baru', $kmBaru)
            ->where('vol_liter', $volLiter)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }

    // =========================================================
    // VALIDASI ODOMETER & HISTORI KENDARAAN
    // =========================================================

    public static function getOdometerTerakhir(int $kendaraanId, ?int $excludeId = null): ?float
    {
        return static::where('kendaraan_id', $kendaraanId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->orderBy('km_baru')
            ->orderByDesc('id')
            ->value('km_baru');
    }

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
    // VALIDASI TIMELINE KENDARAAN
    // =========================================================

    /**
     * Validasi apakah posisi data ini logis dalam timeline kendaraan.
     *
     * Mengembalikan: ['status' => 'Logis'|'Perlu Verifikasi'|'Tidak Logis', 'alasan' => string|null]
     *
     * Timeline dianggap logis jika:
     * 1. km_lama >= km_baru_terakhir tercatat (odometer tidak mundur dari data terakhir)
     * 2. Tidak ada lompatan jarak yang tidak wajar antar perjalanan
     */
    public static function validasiTimeline(
        float   $kmLama,
        float   $kmBaru,
        int     $kendaraanId,
        ?int    $excludeId = null,
        ?string $tanggal   = null
    ): array {
        if ($tanggal === null) {
            return ['status' => 'Logis', 'alasan' => null];
        }

        $riwayat = static::where('kendaraan_id', $kendaraanId)
            ->where(function ($q) use ($tanggal, $excludeId) {
                $q->where('tanggal', '<', $tanggal);
                if ($excludeId) {
                    $q->orWhere(function ($q2) use ($tanggal, $excludeId) {
                        $q2->where('tanggal', '=', $tanggal)
                           ->where('id', '<', $excludeId);
                    });
                } else {
                    $q->orWhere('tanggal', '=', $tanggal);
                }
            })
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get(['km_lama', 'km_baru', 'jarak', 'tanggal', 'id']);

        if ($riwayat->isEmpty()) {
            return ['status' => 'Logis', 'alasan' => null];
        }

        $kmBaruTerakhir = $riwayat->last()->km_baru;

        if ($kmLama > $kmBaruTerakhir) {
            $selisih = round($kmLama - $kmBaruTerakhir, 0);
            return [
                'status' => 'Perlu Verifikasi',
                'alasan' => "Terdapat loncatan odometer sebesar {$selisih} km dari pencatatan terakhir ({$kmBaruTerakhir}) ke KM awal baru ({$kmLama}). Histori kendaraan perlu diverifikasi.",
            ];
        }

        if ($kmLama < $kmBaruTerakhir) {
            $selisih = round($kmBaruTerakhir - $kmLama, 0);
            return [
                'status' => 'Tidak Logis',
                'alasan' => "KM awal ({$kmLama}) lebih rendah dari KM akhir pencatatan sebelumnya ({$kmBaruTerakhir}). Odometer tidak dapat mundur. Histori kendaraan perlu diverifikasi.",
            ];
        }

        if ($kmLama == $kmBaruTerakhir) {
            // KM lanjutan (berurutan) — logis
            $sebelumnya = $riwayat->last();

            if ($sebelumnya->jarak > 0 && $sebelumnya->km_baru == $kmLama) {
                return ['status' => 'Logis', 'alasan' => null];
            }
        }

        $totalJarakRiwayat = $riwayat->sum('jarak');
        $rataJarak = $riwayat->count() > 0 ? $totalJarakRiwayat / $riwayat->count() : 0;

        $jarakBaru = max(0.0, $kmBaru - $kmLama);
        if ($rataJarak > 0 && $jarakBaru > $rataJarak * 3) {
            return [
                'status' => 'Perlu Verifikasi',
                'alasan' => "Jarak tempuh ({$jarakBaru} km) jauh di atas rata-rata perjalanan kendaraan ini ({$rataJarak} km). Histori perlu diverifikasi.",
            ];
        }

        return ['status' => 'Logis', 'alasan' => null];
    }

    // =========================================================
    // INFER BBM
    // =========================================================

    public static function inferBBM(float $hargaPerLiter): string
    {
        if ($hargaPerLiter <= 7500)  return 'solar';
        if ($hargaPerLiter <= 10500) return 'pertalite';
        if ($hargaPerLiter <= 13500) return 'pertamax';
        if ($hargaPerLiter <= 14500) return 'pertamax_turbo';
        return 'pertamina_dex';
    }

    // =========================================================
    // INDIKASI VERIFIKASI — Menggantikan fraud score
    // Mengumpulkan indikasi yang membuat transaksi perlu diverifikasi
    // =========================================================

    /**
     * Kumpulkan semua indikasi yang membuat data perlu diverifikasi.
     * Sistem TIDAK menyimpulkan kecurangan — hanya memberikan indikasi.
     *
     * Setiap indikasi memiliki bobot. Makin banyak indikasi, makin tinggi
     * tingkat verifikasi yang diperlukan.
     *
     * @param  array    $data       Wajib: kendaraan_id, jumlah_biaya, no_bon,
     *                              km_lama, km_baru, tanggal, jarak, efisiensi,
     *                              harga_per_liter
     * @param  ?int     $excludeId  ID record yang sedang di-update
     */
    public static function hitungIndikasiVerifikasi(array $data, ?int $excludeId = null, ?string $tipe = null): array
    {
        $indikasi = [];
        $totalBobot = 0;

        if ($tipe === null) {
            $kendaraan = Kendaraan::find($data['kendaraan_id']);
            $tipe = $kendaraan->jenis ?? 'R4';
        }
        $bbm = static::inferBBM((float) ($data['harga_per_liter'] ?? 0));

        // --- Indikasi 1: Nominal bon ---
        if (!static::isNominalGanjil((float) $data['jumlah_biaya'])) {
            $indikasi[] = 'nominal_bon_kelipatan_bulat';
            $totalBobot  += 30;
        }

        // --- Indikasi 2: No bon duplikat ---
        if (!empty($data['no_bon']) && static::isDuplicateBon($data['no_bon'], $data['kendaraan_id'], $excludeId)) {
            $indikasi[] = 'no_bon_duplikat';
            $totalBobot  += 40;
        }

        // --- Indikasi 3: Jarak harian melebihi batas ---
        if (!static::isJarakWajar((float) $data['jarak'], $tipe, $data['tanggal'], $data['kendaraan_id'], $excludeId)) {
            $indikasi[] = 'jarak_melebihi_batas_harian';
            $totalBobot  += 25;
        }

        // --- Indikasi 4: Harga tidak wajar ---
        $hargaPerLiter = (float) ($data['harga_per_liter'] ?? 0);
        if ($hargaPerLiter > 0 && ($hargaPerLiter < 6000 || $hargaPerLiter > 20000)) {
            $indikasi[] = 'harga_tidak_wajar';
            $totalBobot  += 20;
        }

        // --- Indikasi 5: Efisiensi di luar batas ---
        $efisiensi = (float) $data['efisiensi'];
        $batas     = static::getBatasEfisiensi($tipe, $bbm);

        if ($efisiensi > $batas['anomali_atas'] || $efisiensi < $batas['anomali_bawah']) {
            $indikasi[] = 'efisiensi_di_luar_batas_mutlak';
            $totalBobot  += 15;
        }

        return [
            'total_bobot' => $totalBobot,
            'indikasi'    => $indikasi,
            'tingkat'     => static::interpretasiTingkatVerifikasi($totalBobot),
        ];
    }

    public static function interpretasiTingkatVerifikasi(int $bobot): string
    {
        return match (true) {
            $bobot === 0 => 'Normal',
            $bobot <= 20 => 'Perhatian',
            $bobot <= 50 => 'Perlu Verifikasi',
            default      => 'Anomali',
        };
    }

    // =========================================================
    // KLASIFIKASI & BATAS EFISIENSI
    // =========================================================

    public static function getBatasEfisiensi(string $tipe, ?string $bbm = null): array
    {
        if ($tipe === 'R2') {
            return ['anomali_atas' => 60, 'balance' => 25, 'boros' => 10, 'anomali_bawah' => 3];
        }

        if (in_array($bbm, ['solar', 'pertamina_dex'])) {
            return ['anomali_atas' => 14, 'balance' => 6, 'boros' => 3, 'anomali_bawah' => 1.5];
        }

        return ['anomali_atas' => 20, 'balance' => 10, 'boros' => 5,  'anomali_bawah' => 2];
    }

    public static function tentukanStatus(float $efisiensi, string $tipe = 'R4', ?string $bbm = null): string
    {
        $b = static::getBatasEfisiensi($tipe, $bbm);

        if ($efisiensi > $b['anomali_atas'] || $efisiensi < $b['anomali_bawah']) {
            return 'anomali';
        }

        if ($efisiensi >= $b['balance']) {
            return 'balance';
        }

        if ($efisiensi >= $b['boros']) {
            return 'boros';
        }

        return 'anomali';
    }

    // =========================================================
    // DETEKSI ANOMALI — Single Source of Truth
    // =========================================================

    private const TOLERANCE_RATIO = 0.4;

    /**
     * Hitung seluruh nilai anomali: hasil_sewajarnya, deviasi, status_anomali, keterangan_anomali.
     *
     * Nilai Sewajarnya = volume * balance_threshold (estimasi jarak yang seharusnya bisa ditempuh)
     * Deviasi = |nilai_sewajarnya - jarak_aktual|
     * Toleransi = 40% dari max(nilai_sewajarnya, 1)
     *
     * Status:
     * - Normal: deviasi <= toleransi
     * - Perlu Verifikasi: deviasi > toleransi && deviasi <= 2*toleransi
     * - Anomali: deviasi > 2*toleransi
     */
    public static function hitungAnomali(
        float  $jarak,
        float  $volLiter,
        float  $efisiensi,
        string $tipe = 'R4',
        string $bbm = 'pertalite',
        array  $indikasi = [],
        string $statusEfisiensi = 'balance'
    ): array {
        $batas = static::getBatasEfisiensi($tipe, $bbm);

        $efisiensiWajar = ($batas['balance'] + $batas['anomali_atas']) / 2;
        $nilaiSewajarnya = $volLiter > 0 ? round($volLiter * $efisiensiWajar, 2) : 0;

        $deviasi = round(abs($nilaiSewajarnya - $jarak), 2);
        $toleransi = round(self::TOLERANCE_RATIO * max($nilaiSewajarnya, 1), 2);

        // Tentukan status berdasarkan rasio deviasi terhadap toleransi
        $rasioDeviasi = $toleransi > 0 ? $deviasi / $toleransi : 0;
        if ($rasioDeviasi <= 1.0) {
            $status = 'Normal';
        } elseif ($rasioDeviasi <= 2.0) {
            $status = 'Perlu Verifikasi';
        } else {
            $status = 'Anomali';
        }

        $keterangan = static::generateKeteranganAnomali(
            $jarak, $nilaiSewajarnya, $deviasi, $toleransi,
            $status, $indikasi, $statusEfisiensi
        );

        return [
            'hasil_sewajarnya'   => $nilaiSewajarnya,
            'deviasi'            => $deviasi,
            'status_anomali'     => $status,
            'keterangan_anomali' => $keterangan,
        ];
    }

    /**
     * Buat kalimat keterangan singkat (satu baris) dengan bukti.
     * Bukan hanya status — harus ada pembuktian.
     */
    private static function generateKeteranganAnomali(
        float  $jarak,
        float  $nilaiSewajarnya,
        float  $deviasi,
        float  $toleransi,
        string $status,
        array  $indikasi,
        string $statusEfisiensi
    ): string {
        if ($status === 'Normal') {
            return 'Data dalam batas normal. Tidak ditemukan indikasi yang memerlukan verifikasi.';
        }

        $alasan = [];

        // Pembuktian 1: Jarak vs Nilai Sewajarnya
        if ($jarak > $nilaiSewajarnya && $deviasi > $toleransi) {
            $alasan[] = "Jarak tempuh ({$jarak} km) melebihi nilai sewajarnya ({$nilaiSewajarnya} km)";
        } elseif ($jarak < $nilaiSewajarnya && $deviasi > $toleransi) {
            $alasan[] = "Jarak tempuh ({$jarak} km) lebih rendah dari nilai sewajarnya ({$nilaiSewajarnya} km)";
        }

        // Pembuktian 2: Deviasi melebihi toleransi
        if ($deviasi > $toleransi) {
            $persen = round(($deviasi / max($nilaiSewajarnya, 1)) * 100, 0);
            $alasan[] = "Selisih {$deviasi} km ({$persen}%) melebihi batas toleransi {$toleransi} km";
        }

        // Pembuktian 3: Indikasi spesifik
        if (in_array('jarak_melebihi_batas_harian', $indikasi)) {
            $alasan[] = 'Pemakaian BBM lebih cepat dari estimasi normal';
        }

        if (in_array('efisiensi_di_luar_batas_mutlak', $indikasi)) {
            $alasan[] = 'Perbedaan odometer tidak wajar';
        }

        if (in_array('nominal_bon_kelipatan_bulat', $indikasi)) {
            $alasan[] = 'Nominal bon merupakan kelipatan genap';
        }

        if (in_array('no_bon_duplikat', $indikasi)) {
            $alasan[] = 'Nomor bon duplikat dengan transaksi sebelumnya';
        }

        if (in_array('harga_tidak_wajar', $indikasi)) {
            $alasan[] = 'Harga per liter tidak sesuai range harga BBM';
        }

        if ($statusEfisiensi === 'anomali' && empty($alasan)) {
            $alasan[] = 'Efisiensi BBM berada di luar batas wajar';
        }

        if (empty($alasan)) {
            return 'Data perjalanan perlu diverifikasi lebih lanjut.';
        }

        return implode('. ', $alasan) . '.';
    }
}
