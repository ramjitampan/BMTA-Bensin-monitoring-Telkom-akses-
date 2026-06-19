<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $fillable = [
        'nama',
        'jabatan',
        'divisi',
        'no_hp'
    ];

    Public function perjalanan()
    {
        return $this->hasMany(Perjalanan::class);
    }
}
