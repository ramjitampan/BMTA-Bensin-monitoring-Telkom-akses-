<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use Illuminate\Http\JsonResponse;

class KendaraanApiController extends Controller
{
    public function index(): JsonResponse
    {
        $kendaraans = Kendaraan::paginate(15, ['id', 'plat_nomor', 'merk', 'jenis', 'tahun']);

        return response()->json([
            'message' => 'Data kendaraan berhasil diambil.',
            'data'    => $kendaraans->items(),
            'meta'    => [
                'current_page' => $kendaraans->currentPage(),
                'last_page'    => $kendaraans->lastPage(),
                'per_page'     => $kendaraans->perPage(),
                'total'        => $kendaraans->total(),
            ],
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
