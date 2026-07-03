<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PerjalananResource extends JsonResource
{
    public function toArray(Request $request): array
    {
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
                'km_lama' => $this->km_lama,
                'km_baru' => $this->km_baru,
                'jarak' => $this->jarak,
            ],
            'bbm' => [
                'vol_liter' => $this->vol_liter,
                'harga_per_liter' => $this->harga_per_liter,
                'jumlah_biaya' => $this->jumlah_biaya,
                'no_bon' => $this->no_bon,
                'foto_bon' => $this->foto_bon,
                'foto_bon_url' => $this->foto_bon ? Storage::url($this->foto_bon) : null,
            ],
            'monitoring' => [
                'efisiensi' => $this->efisiensi,
                'status_efisiensi' => $this->status_efisiensi,
                'status_reason' => $this->status_reason,
                'fraud_score' => $this->fraud_score,
                'fraud_flags' => $this->fraud_flags ?? [],
            ],
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
