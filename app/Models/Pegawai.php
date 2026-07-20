<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    use SoftDeletes;
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
