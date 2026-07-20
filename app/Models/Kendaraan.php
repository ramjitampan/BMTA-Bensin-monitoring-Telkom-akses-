<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kendaraan extends Model
{
    use SoftDeletes;
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

    // Kolom DB = 'jenis', 'tipe' adalah alias
    public function getTipeAttribute(): ?string
    {
        return $this->attributes['jenis'] ?? null;
    }

    public function setTipeAttribute(string $value): void
    {
        $this->attributes['jenis'] = $value;
    }

    // Relasi ke perjalanan
    public function perjalanans()
    {
        return $this->hasMany(Perjalanan::class);
    }
}