<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $fillable = [
        'plat_nomor',
        'tipe',
    ];

    public function perjalanans()
    {
        return $this->hasMany(Perjalanan::class);
    }
}