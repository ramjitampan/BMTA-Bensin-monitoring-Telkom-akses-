<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'jumlah_kendaraan' => $this['jumlah_kendaraan'] ?? 0,
            'jumlah_pegawai' => $this['jumlah_pegawai'] ?? 0,
            'jumlah_perjalanan' => $this['jumlah_perjalanan'] ?? 0,
            'rata_rata_efisiensi' => $this['rata_rata_efisiensi'] ?? 0,
            'total_penggunaan_bbm' => $this['total_penggunaan_bbm'] ?? 0,
            'total_biaya_bbm' => $this['total_biaya_bbm'] ?? 0,
        ];
    }
}
