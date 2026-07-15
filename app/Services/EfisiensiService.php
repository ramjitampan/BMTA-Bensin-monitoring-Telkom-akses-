<?php

namespace App\Services;

class EfisiensiService
{
    public function hitungJarak(float $kmLama, float $kmBaru): float
    {
        return max(0.0, round($kmBaru - $kmLama, 2));
    }

    public function hitungVolumeLiter(float $jumlahBiaya, float $hargaPerLiter): float
    {
        if ($hargaPerLiter <= 0) {
            return 0.0;
        }

        return round($jumlahBiaya / $hargaPerLiter, 2);
    }

    public function hitungEfisiensi(float $jarak, float $volLiter): float
    {
        if ($volLiter <= 0) {
            return 0.0;
        }

        return round($jarak / $volLiter, 2);
    }

    public function generateStatusReason(
        float  $efisiensi,
        string $tipe,
        string $status,
        ?string $bbm = null
    ): string {
        $b    = $this->getBatasEfisiensi($tipe, $bbm);
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

    public function getBatasEfisiensi(string $tipe, ?string $bbm = null): array
    {
        if ($tipe === 'R2') {
            return ['anomali_atas' => 60, 'balance' => 25, 'boros' => 10, 'anomali_bawah' => 3];
        }

        if (in_array($bbm, ['solar', 'pertamina_dex'])) {
            return ['anomali_atas' => 14, 'balance' => 6, 'boros' => 3, 'anomali_bawah' => 1.5];
        }

        return ['anomali_atas' => 20, 'balance' => 10, 'boros' => 5,  'anomali_bawah' => 2];
    }

    public function tentukanStatus(float $efisiensi, string $tipe = 'R4', ?string $bbm = null): string
    {
        $b = $this->getBatasEfisiensi($tipe, $bbm);

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

    public function inferBBM(float $hargaPerLiter): string
    {
        if ($hargaPerLiter <= 7500)  return 'solar';
        if ($hargaPerLiter <= 10500) return 'pertalite';
        if ($hargaPerLiter <= 13500) return 'pertamax';
        if ($hargaPerLiter <= 14500) return 'pertamax_turbo';
        return 'pertamina_dex';
    }
}
