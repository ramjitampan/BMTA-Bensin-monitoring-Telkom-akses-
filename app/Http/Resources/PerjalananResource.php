<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PerjalananResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $flags = $this->fraud_flags ?? [];

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
            'timeline_status' => $flags['timeline_status'] ?? 'Logis',
            'alasan_timeline' => $flags['alasan_timeline'] ?? null,
            'display_flags' => $flags['display_flags'] ?? [],

            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
