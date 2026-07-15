<?php

namespace App\Http\Resources;

use App\Models\Perjalanan;
use App\Services\TimelineService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PerjalananResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $flags = $this->fraud_flags ?? [];

        $timeline = app(TimelineService::class)->validasiTimeline(
            (float) $this->km_lama,
            (float) $this->km_baru,
            (int) $this->kendaraan_id,
            $this->id,
            $this->tanggal
        );

        $indikasi = $flags['verifikasi_indikasi'] ?? [];
        $timelineStatus = $flags['timeline_status'] ?? $timeline['status'] ?? 'Logis';
        $displayFlags = $flags['display_flags'] ?? [];

        if (empty($displayFlags) && !empty($indikasi)) {
            foreach ($indikasi as $code) {
                $displayFlags[] = match ($code) {
                    'no_bon_duplikat'              => 'Bon Duplikat',
                    'harga_tidak_wajar'            => 'Harga Tidak Wajar',
                    'nominal_bon_kelipatan_bulat'  => 'Harga Tidak Wajar',
                    'jarak_melebihi_batas_harian'   => 'Volume Tidak Wajar',
                    'efisiensi_di_luar_batas_mutlak' => 'Volume Tidak Wajar',
                    default                        => '',
                };
            }
            if (in_array($timelineStatus, ['Tidak Logis'])) {
                $displayFlags[] = 'Timeline Tidak Logis';
            }
            if (in_array($timelineStatus, ['Perlu Verifikasi'])) {
                $displayFlags[] = 'Odometer Mundur';
            }
            $displayFlags = array_values(array_unique(array_filter($displayFlags)));
        }

        return [
            'id' => $this->id,
            'tanggal' => optional($this->tanggal)->toDateString(),
            'pegawai' => [
                'id' => $this->pegawai_id,
                'nama' => $this->pegawai->nama ?? null,
            ],
            'kendaraan' => [
                'id' => $this->kendaraan_id,
                'plat_nomor' => $this->kendaraan->plat_nomor ?? null,
                'jenis' => $this->kendaraan->jenis ?? null,
            ],
            'tujuan' => $this->tujuan,
            'uraian' => $this->uraian,
            'odometer' => [
                'km_lama' => (float) $this->km_lama,
                'km_baru' => (float) $this->km_baru,
                'jarak_km' => (float) $this->jarak,
            ],
            'bbm' => [
                'vol_liter' => (float) $this->vol_liter,
                'harga_per_liter' => (float) $this->harga_per_liter,
                'jumlah_biaya' => (float) $this->jumlah_biaya,
                'no_bon' => $this->no_bon,
                'foto_bon' => $this->foto_bon,
                'foto_bon_url' => $this->foto_bon ? Storage::url($this->foto_bon) : null,
            ],
            'monitoring' => [
                'efisiensi' => (float) $this->efisiensi,
                'status_efisiensi' => $this->status_efisiensi,
                'status_reason' => $this->status_reason,
                'fraud_score' => $this->fraud_score,
                'fraud_flags' => $this->fraud_flags,
            ],

            'status_validasi' => $flags['status_anomali'] ?? 'Normal',
            'nilai_sewajarnya' => (float) ($flags['hasil_sewajarnya'] ?? 0),
            'deviasi_km' => (float) ($flags['deviasi'] ?? 0),
            'keterangan_validasi' => $flags['keterangan_anomali'] ?? 'Tidak ada alasan.',
            'timeline_status' => $timelineStatus,
            'alasan_timeline' => $timeline['alasan'] ?? $flags['alasan_timeline'] ?? null,
            'display_flags' => $displayFlags,

            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
