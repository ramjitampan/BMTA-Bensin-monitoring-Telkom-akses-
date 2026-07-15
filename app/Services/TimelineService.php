<?php

namespace App\Services;

use App\Models\Perjalanan;

class TimelineService
{
    public function getOdometerTerakhir(int $kendaraanId, ?int $excludeId = null): ?float
    {
        return Perjalanan::where('kendaraan_id', $kendaraanId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->orderBy('km_baru')
            ->orderByDesc('id')
            ->value('km_baru');
    }

    public function validasiTimeline(
        float   $kmLama,
        float   $kmBaru,
        int     $kendaraanId,
        ?int    $excludeId = null,
        ?string $tanggal   = null
    ): array {
        if ($tanggal === null) {
            return ['status' => 'Logis', 'alasan' => null];
        }

        $riwayat = Perjalanan::where('kendaraan_id', $kendaraanId)
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
}
