<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\KendaraanResource;
use App\Models\Kendaraan;
use Illuminate\Http\JsonResponse;

class KendaraanController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => KendaraanResource::collection(
                Kendaraan::orderBy('plat_nomor')->get()
            ),
        ], 200);
    }

    public function show(Kendaraan $kendaraan): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => new KendaraanResource($kendaraan),
        ], 200);
    }
}
