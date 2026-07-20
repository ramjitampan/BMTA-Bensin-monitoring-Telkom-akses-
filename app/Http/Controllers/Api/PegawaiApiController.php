<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\JsonResponse;

class PegawaiApiController extends Controller
{
    public function index(): JsonResponse
    {
        $pegawais = Pegawai::paginate(15, ['id', 'nama', 'jabatan', 'divisi', 'no_hp']);

        return response()->json([
            'message' => 'Data pegawai berhasil diambil.',
            'data'    => $pegawais->items(),
            'meta'    => [
                'current_page' => $pegawais->currentPage(),
                'last_page'    => $pegawais->lastPage(),
                'per_page'     => $pegawais->perPage(),
                'total'        => $pegawais->total(),
            ],
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
