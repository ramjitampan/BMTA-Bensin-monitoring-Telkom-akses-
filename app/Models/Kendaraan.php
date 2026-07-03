<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $table = 'kendaraans';

    protected $fillable = [
        'plat_nomor',
        'merk',
        'jenis',
        'tahun',
    ];

    protected $casts = [
        'tahun' => 'integer',
    ];

    // Relasi ke perjalanan (opsional, siap pakai)
    public function perjalanans()
    {
        return $this->hasMany(\App\Models\Perjalanan::class);
    }
}