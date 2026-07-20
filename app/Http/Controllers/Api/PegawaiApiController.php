<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\JsonResponse;

class PegawaiApiController extends Controller
{
    public function index(): JsonResponse
    {
        $pegawais = Pegawai::all(['id', 'nama', 'jabatan', 'divisi', 'no_hp']);

        return response()->json([
            'message' => 'Data pegawai berhasil diambil.',
            'data'    => $pegawais,
        ]);
    }

    public function show(Pegawai $pegawai): JsonResponse
    {
        return response()->json([
            'message' => 'Detail pegawai berhasil diambil.',
            'data'    => [
                'id'      => $pegawai->id,
                'nama'    => $pegawai->nama,
                'jabatan' => $pegawai->jabatan,
                'divisi'  => $pegawai->divisi,
                'no_hp'   => $pegawai->no_hp,
            ],
        ]);
    }
}
