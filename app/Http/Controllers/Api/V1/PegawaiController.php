<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PegawaiResource;
use App\Models\Pegawai;
use Illuminate\Http\JsonResponse;

class PegawaiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => PegawaiResource::collection(
                Pegawai::orderBy('nama')->get()
            ),
        ], 200);
    }

    public function show(Pegawai $pegawai): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => new PegawaiResource($pegawai),
        ], 200);
    }
}
