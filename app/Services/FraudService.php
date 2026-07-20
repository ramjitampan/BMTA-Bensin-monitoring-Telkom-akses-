<?php

namespace App\Services;

use App\Models\Kendaraan;

class FraudService
{
    private const TOLERANCE_RATIO = 0.4;

    public function __construct(
        private EfisiensiService $efisiensiService,
        private ValidasiService $validasiService
    ) {}

    public function hitungIndikasiVerifikasi(array $data, ?int $excludeId = null, ?string $tipe = null): array
    {
        $indikasi = [];
        $totalBobot = 0;

        if ($tipe === null) {
            $kendaraan = Kendaraan::find($data['kendaraan_id']);
            $tipe = $kendaraan->jenis ?? 'R4';
        }
        $bbm = $this->efisiensiService->inferBBM((float) ($data['harga_per_liter'] ?? 0));

        if (!$this->validasiService->isNominalGanjil((float) $data['jumlah_biaya'])) {
            $indikasi[] = 'nominal_bon_kelipatan_bulat';
            $totalBobot  += 30;
        }

        if (!empty($data['no_bon']) && $this->validasiService->isDuplicateBon($data['no_bon'], $data['kendaraan_id'], $excludeId)) {
            $indikasi[] = 'no_bon_duplikat';
            $totalBobot  += 40;
        }

        if (!$this->validasiService->isJarakWajar((float) $data['jarak'], $tipe, $data['tanggal'], $data['kendaraan_id'], $excludeId)) {
            $indikasi[] = 'jarak_melebihi_batas_harian';
            $totalBobot  += 25;
        }

        $hargaPerLiter = (float) ($data['harga_per_liter'] ?? 0);
        if ($hargaPerLiter > 0 && ($hargaPerLiter < 6000 || $hargaPerLiter > 20000)) {
            $indikasi[] = 'harga_tidak_wajar';
            $totalBobot  += 20;
        }

        $efisiensi = (float) $data['efisiensi'];
        $batas     = $this->efisiensiService->getBatasEfisiensi($tipe, $bbm);

        if ($efisiensi > $batas['anomali_atas'] || $efisiensi < $batas['anomali_bawah']) {
            $indikasi[] = 'efisiensi_di_luar_batas_mutlak';
            $totalBobot  += 15;
        }

        return [
            'total_bobot' => $totalBobot,
            'indikasi'    => $indikasi,
            'tingkat'     => $this->interpretasiTingkatVerifikasi($totalBobot),
        ];
    }

    public function interpretasiTingkatVerifikasi(int $bobot): string
    {
        return match (true) {
            $bobot === 0 => 'Normal',
            $bobot <= 20 => 'Perhatian',
            $bobot <= 50 => 'Perlu Verifikasi',
            default      => 'Anomali',
        };
    }

    public function hitungAnomali(
        float  $jarak,
        float  $volLiter,
        float  $efisiensi,
        string $tipe = 'R4',
        string $bbm = 'pertalite',
        array  $indikasi = [],
        string $statusEfisiensi = 'balance'
    ): array {
        $batas = $this->efisiensiService->getBatasEfisiensi($tipe, $bbm);

        $efisiensiWajar = ($batas['balance'] + $batas['anomali_atas']) / 2;
        $nilaiSewajarnya = $volLiter > 0 ? round($volLiter * $efisiensiWajar, 2) : 0;

        $deviasi = round(abs($nilaiSewajarnya - $jarak), 2);
        $toleransi = round(self::TOLERANCE_RATIO * max($nilaiSewajarnya, 1), 2);

        $rasioDeviasi = $toleransi > 0 ? $deviasi / $toleransi : 0;
        if ($rasioDeviasi <= 1.0) {
            $status = 'Normal';
        } elseif ($rasioDeviasi <= 2.0) {
            $status = 'Perlu Verifikasi';
        } else {
            $status = 'Anomali';
        }

        $keterangan = $this->generateKeteranganAnomali(
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

    private function generateKeteranganAnomali(
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

        if ($jarak > $nilaiSewajarnya && $deviasi > $toleransi) {
            $alasan[] = "Jarak tempuh ({$jarak} km) melebihi nilai sewajarnya ({$nilaiSewajarnya} km)";
        } elseif ($jarak < $nilaiSewajarnya && $deviasi > $toleransi) {
            $alasan[] = "Jarak tempuh ({$jarak} km) lebih rendah dari nilai sewajarnya ({$nilaiSewajarnya} km)";
        }

        if ($deviasi > $toleransi) {
            $persen = round(($deviasi / max($nilaiSewajarnya, 1)) * 100, 0);
            $alasan[] = "Selisih {$deviasi} km ({$persen}%) melebihi batas toleransi {$toleransi} km";
        }

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

    public function resolveDisplayFlags(array $indikasi, string $timelineStatus): array
    {
        $flags = [];
        foreach ($indikasi as $code) {
            $flags[] = match ($code) {
                'no_bon_duplikat'              => 'Bon Duplikat',
                'harga_tidak_wajar'            => 'Harga Tidak Wajar',
                'nominal_bon_kelipatan_bulat'  => 'Harga Tidak Wajar',
                'jarak_melebihi_batas_harian'   => 'Volume Tidak Wajar',
                'efisiensi_di_luar_batas_mutlak' => 'Volume Tidak Wajar',
                default                        => '',
            };
        }
        if (in_array($timelineStatus, ['Tidak Logis'])) {
            $flags[] = 'Timeline Tidak Logis';
        }
        if (in_array($timelineStatus, ['Perlu Verifikasi'])) {
            $flags[] = 'Odometer Mundur';
        }
        return array_values(array_unique(array_filter($flags)));
    }
}
