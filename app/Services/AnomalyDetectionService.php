<?php

namespace App\Services;

use App\Models\Perjalanan;
use Illuminate\Support\Collection;

/**
 * Menghitung deteksi anomali perjalanan secara on-the-fly.
 * 
 * Method getAll() mengembalikan seluruh data perjalanan dengan fraud_score dan fraud_flags
 * hasil komputasi real-time. Method getPerjalananJson() memformat data untuk modal detail.
 */
class AnomalyDetectionService
{
    public function __construct(
        private EfisiensiService $efisiensiService,
        private ValidasiService $validasiService,
        private TimelineService $timelineService,
        private FraudService $fraudService
    ) {}

    public function getAll(): Collection
    {
        return Perjalanan::with(['pegawai', 'kendaraan'])
            ->orderByVehicleTimeline()
            ->get()
            ->map(function (Perjalanan $p): array {
                return $this->computeOnTheFly($p);
            });
    }

    public function getPerjalananJson(): Collection
    {
        return Perjalanan::with(['pegawai', 'kendaraan'])
            ->orderByVehicleTimeline()
            ->get()
            ->map(function (Perjalanan $p): array {
                return $this->computeOnTheFly($p);
            })
            ->map(function (array $item): array {
                $p = $item['perjalanan'];
                $flags = $item['fraud_flags'];
                $efisiensiLabel = match ($p->status_efisiensi) {
                    'balance' => 'Balance',
                    'boros'   => 'Boros',
                    default   => 'Anomali',
                };
                $validasiLabel = $flags['status_anomali'] ?? 'Normal';

                return [
                    'id'               => $p->id,
                    'tanggal'          => $p->tanggal->format('d/m/Y'),
                    'pegawai'          => $p->pegawai->nama ?? '-',
                    'no_polisi'        => $p->kendaraan->nomor_polisi ?? $p->kendaraan->plat_nomor ?? '-',
                    'tipe_kendaraan'   => $p->kendaraan->tipe ?? '-',
                    'tujuan'           => $p->tujuan ?: '-',
                    'uraian'           => $p->uraian ?: '-',
                    'km_lama'          => number_format((float) $p->km_lama, 0, ',', '.'),
                    'km_baru'          => number_format((float) $p->km_baru, 0, ',', '.'),
                    'jarak_aktual'     => number_format((float) $p->jarak, 2, ',', '.') . ' km',
                    'volume_bbm'       => number_format((float) $p->vol_liter, 2, ',', '.') . ' L',
                    'efisiensi'        => number_format((float) $p->efisiensi, 2, ',', '.') . ' km/L',
                    'status_efisiensi' => $efisiensiLabel,
                    'nilai_sewajarnya' => number_format((float) ($flags['hasil_sewajarnya'] ?? 0), 2, ',', '.') . ' km',
                    'deviasi'          => number_format((float) ($flags['deviasi'] ?? 0), 2, ',', '.') . ' km',
                    'timeline_status'  => $item['timeline_status'] ?? 'Logis',
                    'alasan_timeline'  => $item['alasan_timeline'],
                    'status_validasi'  => $validasiLabel,
                    'alasan'           => $flags['keterangan_anomali'] ?? 'Tidak ada alasan.',
                    'display_flags'    => $flags['display_flags'] ?? [],
                ];
            });
    }

    private function computeOnTheFly(Perjalanan $p): array
    {
        $tipe = $p->kendaraan->jenis ?? 'R4';
        $bbm       = $this->efisiensiService->inferBBM((float) $p->harga_per_liter);

        $verifikasiResult = $this->fraudService->hitungIndikasiVerifikasi([
            'kendaraan_id'    => $p->kendaraan_id,
            'jumlah_biaya'    => $p->jumlah_biaya,
            'no_bon'          => $p->no_bon,
            'km_lama'         => $p->km_lama,
            'km_baru'         => $p->km_baru,
            'tanggal'         => $p->tanggal->format('Y-m-d'),
            'jarak'           => $p->jarak,
            'efisiensi'       => $p->efisiensi,
            'harga_per_liter' => $p->harga_per_liter,
        ], $p->id, $tipe);

        $anomaliResult = $this->fraudService->hitungAnomali(
            (float) $p->jarak,
            (float) $p->vol_liter,
            (float) $p->efisiensi,
            $tipe,
            $bbm,
            $verifikasiResult['indikasi'],
            $p->status_efisiensi ?? 'balance',
        );

        $timeline = $this->timelineService->validasiTimeline(
            (float) $p->km_lama,
            (float) $p->km_baru,
            (int) $p->kendaraan_id,
            $p->id,
            $p->tanggal
        );

        $statusAnomali = $anomaliResult['status_anomali'] ?? 'Normal';
        $fraudScore = match($statusAnomali) {
            'Perlu Verifikasi' => 50,
            'Anomali'          => ($p->status_efisiensi === 'balance') ? 50 : 90,
            default => ($p->status_efisiensi === 'anomali') ? 50 : 10,
        };
        $displayFlags = $this->fraudService->resolveDisplayFlags(
            $verifikasiResult['indikasi'],
            $timeline['status'] ?? 'Logis'
        );
        $fraudFlags = [
            'verifikasi_indikasi' => $verifikasiResult['indikasi'],
            'total_bobot'         => $verifikasiResult['total_bobot'],
            'status_anomali'      => $statusAnomali,
            'hasil_sewajarnya'    => $anomaliResult['hasil_sewajarnya'],
            'deviasi'             => $anomaliResult['deviasi'],
            'keterangan_anomali'  => $anomaliResult['keterangan_anomali'],
            'timeline_status'     => $timeline['status'] ?? 'Logis',
            'alasan_timeline'     => $timeline['alasan'],
            'display_flags'       => $displayFlags,
        ];

        return [
            'perjalanan'       => $p,
            'selisih_aktual'   => (float) $p->jarak,
            'nilai_sewajarnya' => (float) ($anomaliResult['hasil_sewajarnya'] ?? 0),
            'deviasi'          => (float) ($anomaliResult['deviasi'] ?? 0),
            'status'           => $statusAnomali,
            'keterangan'       => $anomaliResult['keterangan_anomali'] ?? 'Tidak ditemukan anomali.',
            'timeline_status'  => $timeline['status'] ?? 'Logis',
            'alasan_timeline'  => $timeline['alasan'],
            'fraud_score'      => $fraudScore,
            'fraud_flags'      => $fraudFlags,
        ];
    }
}
