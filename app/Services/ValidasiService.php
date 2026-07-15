<?php

namespace App\Services;

use App\Models\Kendaraan;
use App\Models\Perjalanan;

class ValidasiService
{
    public function isNominalGanjil(float $jumlah): bool
    {
        $jumlahInt = (int) round($jumlah);

        if ($jumlahInt % 1000 !== 0) {
            return false;
        }

        return $jumlahInt % 10000 !== 0;
    }

    public function isDuplicateBon(string $noBon, int $kendaraanId, ?int $excludeId = null): bool
    {
        return Perjalanan::where('no_bon', $noBon)
            ->where('kendaraan_id', $kendaraanId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }

    public function isDuplicateRecord(
        string $tanggal,
        int $kendaraanId,
        float $kmLama,
        float $kmBaru,
        float $volLiter,
        ?int $excludeId = null
    ): bool {
        return Perjalanan::whereDate('tanggal', $tanggal)
            ->where('kendaraan_id', $kendaraanId)
            ->where('km_lama', $kmLama)
            ->where('km_baru', $kmBaru)
            ->where('vol_liter', $volLiter)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }

    public function isJarakWajar(
        float   $jarak,
        string  $tipe,
        string  $tanggal,
        int     $kendaraanId,
        ?int    $excludeId = null
    ): bool {
        $maxPerHari = $tipe === 'R2' ? 200 : 600;

        $jarakHariIni = Perjalanan::where('kendaraan_id', $kendaraanId)
            ->whereDate('tanggal', $tanggal)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->sum('jarak');

        return ($jarakHariIni + $jarak) <= $maxPerHari;
    }
}
