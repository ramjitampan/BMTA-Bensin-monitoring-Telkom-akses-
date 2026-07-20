<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use Illuminate\Http\JsonResponse;

class KendaraanApiController extends Controller
{
    public function index(): JsonResponse
    {
        $kendaraans = Kendaraan::all(['id', 'plat_nomor', 'merk', 'jenis', 'tahun']);

        return response()->json([
            'message' => 'Data kendaraan berhasil diambil.',
            'data'    => $kendaraans,
        ]);
    }

    public function show(Kendaraan $kendaraan): JsonResponse
    {
        return response()->json([
            'message' => 'Detail kendaraan berhasil diambil.',
            'data'    => [
                'id'         => $kendaraan->id,
                'plat_nomor' => $kendaraan->plat_nomor,
                'merk'       => $kendaraan->merk,
                'jenis'      => $kendaraan->jenis,
                'tahun'      => $kendaraan->tahun,
            ],
        ]);
    }
}
