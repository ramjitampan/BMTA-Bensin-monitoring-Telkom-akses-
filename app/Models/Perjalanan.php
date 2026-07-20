<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perjalanan extends Model
{
    use SoftDeletes;
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

    public function scopeOrderByVehicleTimeline($query)
    {
        return $query->orderBy('kendaraan_id')->orderBy('km_baru');
    }
}
